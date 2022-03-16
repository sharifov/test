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
use Yii;
use yii\db\Transaction;
use yii\helpers\Html;

/**
 * Class LeadToExtraQueueService
 */
class LeadToExtraQueueService
{
    private int $leadId;
    private string $dataKey;
    private LeadRepository $leadRepository;

    public function __construct(int $leadId, string $dataKey, LeadRepository $leadRepository)
    {
        $this->leadId = $leadId;
        $this->dataKey = $dataKey;
        $this->leadRepository = $leadRepository;
    }

    public function handle(): void
    {
        $transaction = new Transaction(['db' => Yii::$app->db]);

        try {
            $transaction->begin();

            if (!$lead = Lead::find()->where(['id' => $this->leadId])->limit(1)->one()) {
                throw new \RuntimeException('Lead not found by ID(' . $this->leadId . ')');
            }
            $lppChecker = new LeadPoorProcessingChecker($lead, $this->dataKey);
            if (!$lppChecker->isChecked()) {
                throw new \RuntimeException('Lpp check is failed. Lead(' . $this->leadId . ') : LPPD(' . $this->dataKey . ')');
            }

            $ownerId = $lead->employee_id;
            $reason = Html::encode($lppChecker->getRule()->lppd_description);
            $lead->extraQueue($ownerId, null, $reason);
            $this->leadRepository->save($lead);
            LeadPoorProcessing::deleteAll(['lpp_lead_id' => $lead->id]);

            $transaction->commit();
        } catch (\Throwable $throwable) {
            $transaction->rollBack();
            throw $throwable;
        }

        $description = sprintf(LeadPoorProcessingLogStatus::REASON_ADDED_TO_EXTRA_QUEUE_ACCORDING_TO_THE_RULE, $reason);
        $leadPoorProcessingLog = LeadPoorProcessingLog::create(
            $lead->id,
            $lppChecker->getRule()->lppd_id,
            $ownerId,
            LeadPoorProcessingLogStatus::STATUS_ADDED_TO_EXTRA_QUEUE,
            $description
        );
        if (!$leadPoorProcessingLog->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($leadPoorProcessingLog, ' '));
        }

        $leadPoorProcessingLogRepository = new LeadPoorProcessingLogRepository($leadPoorProcessingLog);
        $leadPoorProcessingLogRepository->save();

        if ($lead->hasOwner()) {
            $message = 'Lead(' . Purifier::createLeadShortLink($lead) . ') transfer to Extra Queue. ' . $reason;

            Notifications::createAndPublish(
                $ownerId,
                'Lead transfer to Extra Queue',
                $message,
                Notifications::TYPE_INFO,
                true
            );
        }
    }
}
