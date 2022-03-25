<?php

namespace src\model\leadPoorProcessing\service\rules;

use common\models\Lead;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessing\entity\LeadPoorProcessingQuery;
use src\model\leadPoorProcessing\repository\LeadPoorProcessingRepository;
use src\model\leadPoorProcessing\service\LeadPoorProcessingChecker;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadPoorProcessingLog\repository\LeadPoorProcessingLogRepository;

/**
 * Class AbstractLeadPoorProcessingService
 */
class AbstractLeadPoorProcessingService implements LeadPoorProcessingServiceInterface
{
    private Lead $lead;
    private LeadPoorProcessingData $rule;
    private ?string $description = null;

    public function __construct(int $leadId, string $ruleKey, ?string $description = null)
    {
        if (!$lead = Lead::find()->where(['id' => $leadId])->limit(1)->one()) {
            throw new \RuntimeException('Lead not found by ID(' . $leadId . ')');
        }
        $this->lead = $lead;

        if (!$rule = LeadPoorProcessingDataQuery::getRuleByKey($ruleKey)) {
            throw new \RuntimeException('Rule not found by key(' . $ruleKey . ')');
        }
        $this->rule = $rule;
        $this->description = $description;
    }

    /**
     * @return bool
     */
    public function checkCondition(): bool
    {
        (new LeadPoorProcessingChecker($this->getLead(), $this->getRule()->lppd_key))->check();
        return true;
    }

    public function handle(): void
    {
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
            $this->description
        );

        $leadPoorProcessingRepository = new LeadPoorProcessingRepository($leadPoorProcessing);
        $leadPoorProcessingRepository->save(true);

        $leadPoorProcessingLogRepository = new LeadPoorProcessingLogRepository($leadPoorProcessingLog);
        $leadPoorProcessingLogRepository->save(true);
    }

    public function getExpiration(string $format = 'Y-m-d H:i:s'): string
    {
        return (new \DateTimeImmutable())
            ->modify('+ ' . $this->getRule()->lppd_minute . ' minutes')
            ->format($format);
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }

    public function getRule(): LeadPoorProcessingData
    {
        return $this->rule;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }
}
