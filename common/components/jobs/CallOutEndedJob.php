<?php

namespace common\components\jobs;

use common\models\Call;
use common\models\Client;
use modules\webEngage\form\WebEngageEventForm;
use modules\webEngage\settings\WebEngageDictionary;
use modules\webEngage\src\service\webEngageEventData\call\CallEventService;
use modules\webEngage\src\service\webEngageEventData\call\eventData\FirstCallNotPickedEventData;
use modules\webEngage\src\service\webEngageEventData\call\eventData\LeadFirstCallNotPickedEventData;
use modules\webEngage\src\service\webEngageEventData\call\eventData\UserPickedCallEventData;
use modules\webEngage\src\service\WebEngageRequestService;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use sales\model\clientData\entity\ClientData;
use sales\model\clientData\entity\ClientDataQuery;
use sales\model\clientDataKey\entity\ClientDataKeyDictionary;
use sales\model\clientDataKey\service\ClientDataKeyService;
use sales\model\leadData\services\LeadDataCreateService;
use sales\model\leadDataKey\services\LeadDataKeyDictionary;
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

                if ($clientData && $clientData->cd_field_value <= 1 && $call->isStatusNoAnswer()) {
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
                if ($call->c_call_duration >= SettingHelper::getUserPrickedCallDuration() && $call->isStatusCompleted()) {
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
        }
    }
}
