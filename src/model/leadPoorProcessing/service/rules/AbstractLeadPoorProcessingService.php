<?php

namespace src\model\leadPoorProcessing\service\rules;

use common\models\Lead;
use src\helpers\ErrorsToStringHelper;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessing\entity\LeadPoorProcessingQuery;
use src\model\leadPoorProcessing\repository\LeadPoorProcessingRepository;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadPoorProcessingLog\repository\LeadPoorProcessingLogRepository;

/**
 * Class AbstractLeadPoorProcessingService
 */
class AbstractLeadPoorProcessingService
{
    private Lead $lead;
    private LeadPoorProcessingData $rule;

    public function __construct(int $leadId, string $ruleKey)
    {
        if (!$lead = Lead::find()->where(['id' => $leadId])->limit(1)->one()) {
            throw new \RuntimeException('Lead not found by ID(' . $leadId . ')');
        }
        $this->lead = $lead;

        if (!$rule = LeadPoorProcessingDataQuery::getRuleByKey($ruleKey)) {
            throw new \RuntimeException('Rule not found by key(' . LeadPoorProcessingDataDictionary::KEY_LAST_ACTION . ')');
        }
        $this->rule = $rule;
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
            $logStatus
        );

        $leadPoorProcessingRepository = new LeadPoorProcessingRepository($leadPoorProcessing);
        $leadPoorProcessingRepository->save(true);

        $leadPoorProcessingLogRepository = new LeadPoorProcessingLogRepository($leadPoorProcessingLog);
        $leadPoorProcessingLogRepository->save(true);
    }

    public function getExpiration(): string
    {
        return (new \DateTimeImmutable())
            ->modify('+ ' . $this->getRule()->lppd_minute . ' minutes')
            ->format('Y-m-d H:i:s');
    }

    public function getLead(): Lead
    {
        return $this->lead;
    }

    public function getRule(): LeadPoorProcessingData
    {
        return $this->rule;
    }
}
