<?php

namespace src\model\leadPoorProcessing\service;

use common\components\purifier\Purifier;
use common\models\Lead;
use common\models\Notifications;
use src\helpers\ErrorsToStringHelper;
use src\model\leadPoorProcessing\entity\LeadPoorProcessing;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLog;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadPoorProcessingLog\repository\LeadPoorProcessingLogRepository;
use src\repositories\lead\LeadRepository;
use yii\helpers\Html;

/**
 * Class LeadToExtraQueueService
 */
class LeadToExtraQueueService
{
    private Lead $lead;
    private LeadPoorProcessingData $rule;
    private LeadRepository $leadRepository;

    public function __construct(int $leadId, int $lppDataId, LeadRepository $leadRepository)
    {
        if (!$lead = Lead::find()->where(['id' => $leadId])->limit(1)->one()) {
            throw new \RuntimeException('Lead not found by ID(' . $leadId . ')');
        }
        $this->lead = $lead;

        if (!$rule = LeadPoorProcessingDataQuery::getRuleById($lppDataId)) {
            throw new \RuntimeException('Rule not found by key(' . LeadPoorProcessingDataDictionary::KEY_LAST_ACTION . ')');
        }
        $this->rule = $rule;
        $this->leadRepository = $leadRepository;
    }

    public function handle(): void
    {
        $reason = Html::encode($this->getRule()->lppd_description);
        $ownerId = $this->getLead()->employee_id;

        $this->getLead()->extraQueue($ownerId, null, $reason);

        if ($this->getLead()->hasOwner()) {
            $message = 'Lead(' . Purifier::createLeadShortLink($this->getLead()) . ') transfer to Extra Queue. ' . $reason;

            Notifications::createAndPublish(
                $ownerId,
                'Lead transfer to Extra Queue',
                $message,
                Notifications::TYPE_INFO,
                true
            );
        }

        $this->leadRepository->save($this->getLead());

        LeadPoorProcessing::deleteAll(['lpp_lead_id' => $this->getLead()->id]);

        $description = sprintf(LeadPoorProcessingLogStatus::REASON_ADDED_TO_EXTRA_QUEUE_ACCORDING_TO_THE_RULE, $reason);
        $leadPoorProcessingLog = LeadPoorProcessingLog::create(
            $this->getLead()->id,
            $this->getRule()->lppd_id,
            $ownerId,
            LeadPoorProcessingLogStatus::STATUS_ADDED_TO_EXTRA_QUEUE,
            $description
        );
        if (!$leadPoorProcessingLog->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($leadPoorProcessingLog, ' '));
        }

        $leadPoorProcessingLogRepository = new LeadPoorProcessingLogRepository($leadPoorProcessingLog);
        $leadPoorProcessingLogRepository->save();
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
