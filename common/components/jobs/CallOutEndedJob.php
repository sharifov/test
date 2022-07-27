<?php

namespace common\components\jobs;

use common\models\Call;
use common\models\Client;
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
 * Class CallOutEndedJob
 * @package common\components\jobs
 *
 * @property int $clientId
 * @property int $callId
 */
class CallOutEndedJob extends BaseJob implements JobInterface
{
    private int $clientId;
    private int $callId;

    public function __construct(int $clientId, int $callId, ?float $timeStart = null, $config = [])
    {
        $this->clientId = $clientId;
        $this->callId = $callId;
        parent::__construct($timeStart, $config);
    }

    /**
     * @inheritDoc
     */
    public function execute($queue)
    {
        $this->waitingTimeRegister();

        if ($call = Call::findOne($this->callId)) {
            $keyId = ClientDataKeyService::getIdByKeyCache(ClientDataKeyDictionary::APP_CALL_OUT_TOTAL_COUNT);
            if ($keyId) {
                ClientDataQuery::createOrIncrementValue($this->clientId, $keyId, new \DateTimeImmutable());
            }

            try {
                if (!$client = Client::findOne($this->clientId)) {
                    throw new \RuntimeException('Client not found by ID(' . $this->clientId . ')');
                }
            } catch (\Throwable $throwable) {
                \Yii::warning(AppHelper::throwableLog($throwable), 'CallOutEndedJob:Throwable');
                return;
            }

            try {
                $clientData = ClientData::findOne(['cd_key_id' => $keyId, 'cd_client_id' => $this->clientId]);
                $webEngageUserService = new WebEngageUserService(WebEngageDictionary::EVENT_CALL_FIRST_CALL_NOT_PICKED, $client);
                if ($clientData && $clientData->cd_field_value <= 1 && $call->isStatusNoAnswer() && $webEngageUserService->isSendUserCreateRequest()) {
                    $data = [
                        'userId' => $client->uuid,
                        'eventName' => WebEngageDictionary::EVENT_CALL_FIRST_CALL_NOT_PICKED,
                        'eventTime' => date('Y-m-d\TH:i:sO'),
                    ];

                    if ($lead = CallEventService::getLead($this->callId, $this->clientId)) {
                        $data['eventData'] = (new FirstCallNotPickedEventData($lead))->getEventData();
                    }

                    $webEngageEventForm = new WebEngageEventForm();
                    if (!$webEngageEventForm->load($data)) {
                        throw new \RuntimeException('WebEngageEventForm not loaded');
                    }
                    $webEngageRequestService = new WebEngageRequestService();
                    $webEngageRequestService->addEvent($webEngageEventForm);
                }
            } catch (\RuntimeException | \DomainException $throwable) {
                \Yii::warning(AppHelper::throwableLog($throwable), 'CallOutEndedJob:FirstCallNotPicked:Exception');
            } catch (\Throwable $throwable) {
                \Yii::error(AppHelper::throwableLog($throwable, true), 'CallOutEndedJob:FirstCallNotPicked:Throwable');
            }

            try {
                $webEngageUserService = new WebEngageUserService(WebEngageDictionary::EVENT_CALL_USER_PICKED_CALL, $client);
                if ($call->c_call_duration >= SettingHelper::getUserPrickedCallDuration() && $call->isStatusCompleted() && $webEngageUserService->isSendUserCreateRequest()) {
                    $data = [
                        'userId' => $client->uuid,
                        'eventName' => WebEngageDictionary::EVENT_CALL_USER_PICKED_CALL,
                        'eventTime' => date('Y-m-d\TH:i:sO')
                    ];

                    if ($lead = CallEventService::getLead($this->callId, $this->clientId)) {
                        $data['eventData'] = (new UserPickedCallEventData($lead))->getEventData();
                    }

                    $webEngageEventForm = new WebEngageEventForm();
                    if (!$webEngageEventForm->load($data)) {
                        throw new \RuntimeException('WebEngageEventForm not loaded');
                    }
                    $webEngageRequestService = new WebEngageRequestService();
                    $webEngageRequestService->addEvent($webEngageEventForm);
                }
            } catch (\RuntimeException | \DomainException $throwable) {
                \Yii::warning(AppHelper::throwableLog($throwable), 'CallOutEndedJob:UserPickedCall:Exception');
            } catch (\Throwable $throwable) {
                \Yii::error(AppHelper::throwableLog($throwable, true), 'CallOutEndedJob:UserPickedCall:Throwable');
            }

            try {
                if (
                    $call->isStatusNoAnswer() &&
                    ($lead = CallEventService::getLead($this->callId, $this->clientId)) &&
                    !LeadDataCreateService::isExist($lead->id, LeadDataKeyDictionary::KEY_WE_FIRST_CALL_NOT_PICKED)
                ) {
                    $data = [
                        'userId' => $client->uuid,
                        'eventName' => WebEngageDictionary::EVENT_LEAD_FIRST_CALL_NOT_PICKED,
                        'eventTime' => date('Y-m-d\TH:i:sO'),
                        'eventData' => (new LeadFirstCallNotPickedEventData($lead))->getEventData(),
                    ];

                    $webEngageEventForm = new WebEngageEventForm();
                    if (!$webEngageEventForm->load($data)) {
                        throw new \RuntimeException('WebEngageEventForm not loaded');
                    }
                    $webEngageRequestService = new WebEngageRequestService();
                    $webEngageRequestService->addEvent($webEngageEventForm);

                    (new LeadDataCreateService())->createWeFirstCallNotPicked($lead);
                }
            } catch (\RuntimeException | \DomainException $throwable) {
                \Yii::warning(AppHelper::throwableLog($throwable), 'CallOutEndedJob:LeadFirstCallNotPicked:Exception');
            } catch (\Throwable $throwable) {
                \Yii::error(AppHelper::throwableLog($throwable, true), 'CallOutEndedJob:LeadFirstCallNotPicked:Throwable');
            }

            try {
                if (($lead = $call->cLead) && $lead->isProcessing()) {
                    LeadPoorProcessingService::addLeadPoorProcessingRemoverJob(
                        $lead->id,
                        [LeadPoorProcessingDataDictionary::KEY_NO_ACTION, LeadPoorProcessingDataDictionary::KEY_EXPERT_IDLE],
                        LeadPoorProcessingLogStatus::REASON_CALL
                    );
                }
            } catch (\RuntimeException | \DomainException $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['callId' => $this->callId]);
                \Yii::warning($message, 'CallOutEndedJob:addLeadPoorProcessingRemoverJob:Exception');
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['callId' => $this->callId]);
                \Yii::warning($message, 'CallOutEndedJob:addLeadPoorProcessingRemoverJob:Throwable');
            }

            try {
                if (($lead = $call->cLead) && $lead->employee_id && $lead->isProcessing()) {
                    $leadUserData = LeadUserData::create(
                        LeadUserDataDictionary::TYPE_CALL_OUT,
                        $lead->id,
                        $lead->employee_id,
                        (new \DateTimeImmutable())
                    );
                    (new LeadUserDataRepository($leadUserData))->save(true);
                }
            } catch (\RuntimeException | \DomainException $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['callId' => $this->callId]);
                \Yii::warning($message, 'CallOutEndedJob:addLeadPoorProcessingRemoverJob:Exception');
            } catch (\Throwable $throwable) {
                $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), ['callId' => $this->callId]);
                \Yii::warning($message, 'CallOutEndedJob:addLeadPoorProcessingRemoverJob:Throwable');
            }
            $this->checkAndDeleteFromBusinessExtraQueue($call);
        }
    }

    private function checkAndDeleteFromBusinessExtraQueue(Call $call)
    {
        try {
            /** @fflag FFlag::FF_KEY_BEQ_ENABLE, Business Extra Queue enable */
            if (!\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_BEQ_ENABLE)) {
                return;
            }
            if ($lead = CallEventService::getLead($this->callId, $this->clientId)) {
                if (!$lead->isBusinessType()) {
                    return;
                }
                if ($call->c_call_duration >= SettingHelper::getUserPrickedCallDuration() && $call->isStatusCompleted()) {
                    LeadBusinessExtraQueueService::addLeadBusinessExtraQueueRemoverJob($lead->id, LeadBusinessExtraQueueLogStatus::REASON_CALL);
                } elseif (($call->isDeclined() || $call->isStatusNoAnswer()) && LeadBusinessExtraQueueLogQuery::isLeadWasInBusinessExtraQueue($lead->id)) {
                    LeadBusinessExtraQueueService::addLeadBusinessExtraQueueRemoverJob($lead->id, LeadBusinessExtraQueueLogStatus::REASON_FAILED_CALL);
                }
                return;
            }
            throw new \DomainException('Lead not found, callId - ' . $call->c_id . ' clientId - ' . $this->clientId);
        } catch (\RuntimeException | \DomainException $throwable) {
            \Yii::warning(AppHelper::throwableLog($throwable), 'CallOutEndedJob:UserPickedCall:checkAndDeleteFromBusinessExtraQueue:Exception');
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable, true), 'CallOutEndedJob:UserPickedCall:checkAndDeleteFromBusinessExtraQueue:Throwable');
        }
    }
}
