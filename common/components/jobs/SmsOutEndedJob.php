<?php

namespace common\components\jobs;

use common\models\Call;
use common\models\Client;
use common\models\Sms;
use modules\featureFlag\FFlag;
use modules\webEngage\form\WebEngageEventForm;
use modules\webEngage\settings\WebEngageDictionary;
use modules\webEngage\src\service\webEngageEventData\call\CallEventService;
use modules\webEngage\src\service\webEngageEventData\call\eventData\FirstCallNotPickedEventData;
use modules\webEngage\src\service\webEngageEventData\call\eventData\LeadFirstCallNotPickedEventData;
use modules\webEngage\src\service\webEngageEventData\call\eventData\UserPickedCallEventData;
use modules\webEngage\src\service\WebEngageRequestService;
use modules\webEngage\src\service\webEngageUserData\WebEngageUserService;
use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use src\model\clientData\entity\ClientData;
use src\model\clientData\entity\ClientDataQuery;
use src\model\clientDataKey\entity\ClientDataKeyDictionary;
use src\model\clientDataKey\service\ClientDataKeyService;
use src\model\leadBusinessExtraQueue\service\LeadBusinessExtraQueueService;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogQuery;
use src\model\leadBusinessExtraQueueLog\entity\LeadBusinessExtraQueueLogStatus;
use src\model\leadData\services\LeadDataCreateService;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use src\model\leadPoorProcessing\service\LeadPoorProcessingService;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataDictionary;
use src\model\leadUserData\repository\LeadUserDataRepository;
use src\model\leadPoorProcessingLog\entity\LeadPoorProcessingLogStatus;
use src\model\leadUserData\entity\LeadUserData;
use src\model\leadUserData\entity\LeadUserDataDictionary;
use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;

/**
 * Class SmsOutEndedJob
 * @package common\components\jobs
 *
 * @property int $smsId
 */
class SmsOutEndedJob extends BaseJob implements JobInterface
{
    private int $smsId;

    /**
     * @param int $smsId
     * @param float|null $timeStart
     * @param $config
     */
    public function __construct(int $smsId, ?float $timeStart = null, $config = [])
    {
        $this->smsId = $smsId;
        parent::__construct($timeStart, $config);
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->waitingTimeRegister();

        if ($sms = Sms::find()->where(['s_id' => $this->smsId])->limit(1)->one()) {
            $tplType = $sms->sTemplateType->stp_key ?? null;
            try {
                if ($sms->s_lead_id && LeadPoorProcessingService::checkSmsTemplate($tplType)) {
                    LeadPoorProcessingService::addLeadPoorProcessingRemoverJob(
                        $sms->s_lead_id,
                        [LeadPoorProcessingDataDictionary::KEY_NO_ACTION, LeadPoorProcessingDataDictionary::KEY_EXPERT_IDLE],
                        LeadPoorProcessingLogStatus::REASON_SMS
                    );
                }
            } catch (\RuntimeException | \DomainException $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['callId' => $this->callId]);
                \Yii::warning($message, 'SmsOutEndedJob:addLeadPoorProcessingRemoverJob:Exception');
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['callId' => $this->callId]);
                \Yii::warning($message, 'SmsOutEndedJob:addLeadPoorProcessingRemoverJob:Throwable');
            }

            try {
                if ($sms->s_lead_id && LeadPoorProcessingService::checkSmsTemplate($tplType)) {
                    if (($lead = $sms->sLead) && $lead->employee_id && $lead->isProcessing()) {
                        $leadUserData = LeadUserData::create(
                            LeadUserDataDictionary::TYPE_SMS_OUT,
                            $lead->id,
                            $lead->employee_id,
                            (new \DateTimeImmutable())
                        );
                        (new LeadUserDataRepository($leadUserData))->save(true);
                    }
                }
            } catch (\RuntimeException | \DomainException $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['callId' => $this->callId]);
                \Yii::warning($message, 'SmsOutEndedJob:addLeadUserData:Exception');
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['callId' => $this->callId]);
                \Yii::warning($message, 'SmsOutEndedJob:addLeadUserData:Throwable');
            }
            $lead = $sms->sLead;
            if (
                \Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE)
                && isset($lead)
                && $lead->isBusinessType()
                && LeadBusinessExtraQueueLogQuery::isLeadWasInBusinessExtraQueue($lead->id)
            ) {
                try {
                    if ($sms->s_lead_id && LeadBusinessExtraQueueService::checkSmsTemplate($tplType)) {
                        LeadBusinessExtraQueueService::addLeadBusinessExtraQueueRemoverJob(
                            $sms->s_lead_id,
                            LeadBusinessExtraQueueLogStatus::REASON_SENT_SMS
                        );
                    }
                } catch (\RuntimeException | \DomainException $throwable) {
                    $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['callId' => $this->callId]);
                    \Yii::warning($message, 'SmsOutEndedJob:addLeadBusinessExtraQueueRemoverJob:Exception');
                } catch (\Throwable $throwable) {
                    $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['callId' => $this->callId]);
                    \Yii::warning($message, 'SmsOutEndedJob:addLeadBusinessExtraQueueRemoverJob:Throwable');
                }
            }
        }
    }
}
