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
class LeadPoorProcessingLastAction extends AbstractLeadPoorProcessingService
{
    private bool $isCheckDuplicate = true;
    private int $pauseSecond = 5;

    public function handle(): void
    {
        if (!$this->checkDuplicate()) {
            return;
        }
        parent::handle();
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
