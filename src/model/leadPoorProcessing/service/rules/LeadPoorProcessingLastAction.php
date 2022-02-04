<?php

namespace src\model\leadPoorProcessing\service\rules;

use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessing\entity\LeadPoorProcessingQuery;
use src\model\leadPoorProcessing\repository\LeadPoorProcessingRepository;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadPoorProcessingLog\repository\LeadPoorProcessingLogRepository;

/**
 * Class LeadPoorProcessingLastAction
 */
class LeadPoorProcessingLastAction extends AbstractLeadPoorProcessingService implements LeadPoorProcessingServiceInterface
{
    private bool $isCheckDuplicate = true;
    private int $pauseSecond = 5;

    public function checkCondition(): bool
    {
        if (!$this->getRule()->isEnabled()) {
            throw new \RuntimeException('Rule (' . $this->getRule()->lppd_key . ') not enabled');
        }
        if (!$this->getLead()->isProcessing()) {
            throw new \RuntimeException('Lead (' . $this->getLead()->id . ') not in status "processing"');
        }
        return true;
    }

    public function handle(): void
    {
        if (!$this->checkDuplicate()) {
            return;
        }

        if (!$leadPoorProcessing = LeadPoorProcessingQuery::getByLeadAndKey($this->getLead()->id, $this->getRule()->lppd_id)) {
            $leadPoorProcessing = LeadPoorProcessing::create(
                $this->getLead()->id,
                $this->getRule()->lppd_id,
                $this->getExpiration()
            );
            $logStatus = LeadPoorProcessingLogStatus::STATUS_CREATED;
        } else {
            $leadPoorProcessing->lpp_expiration_dt = $this->getExpiration();
            $logStatus = LeadPoorProcessingLogStatus::STATUS_UPDATED;
        }

        $leadPoorProcessingLog = LeadPoorProcessingLog::create(
            $this->getLead()->id,
            $this->getRule()->lppd_id,
            $this->getLead()->employee_id,
            $logStatus,
            $this->getDescription()
        );

        $leadPoorProcessingRepository = new LeadPoorProcessingRepository($leadPoorProcessing);
        $leadPoorProcessingRepository->save(true);

        $leadPoorProcessingLogRepository = new LeadPoorProcessingLogRepository($leadPoorProcessingLog);
        $leadPoorProcessingLogRepository->save(true);
    }

    public function isCheckDuplicate(): bool
    {
        return $this->isCheckDuplicate;
    }

    public function setCheckDuplicate(bool $checkDuplicate): void
    {
        $this->isCheckDuplicate = $checkDuplicate;
    }

    public function getPauseSecond(): int
    {
        return $this->pauseSecond;
    }

    public function setPauseSecond(int $pauseSecond): void
    {
        $this->pauseSecond = $pauseSecond;
    }

    private function checkDuplicate(): bool
    {
        if (!$this->isCheckDuplicate()) {
            return true;
        }

        $idKey = $this->getRule()->lppd_key . '_' . $this->getLead()->id;
        $redis = \Yii::$app->redis;

        if (!$redis->get($idKey)) {
            $redis->setex($idKey, $this->getPauseSecond(), true);
            return true;
        }
        return false;
    }
}
