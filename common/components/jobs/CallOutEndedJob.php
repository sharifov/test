<?php

namespace common\components\jobs;

use common\models\Call;
use modules\webEngage\form\WebEngageEventForm;
use modules\webEngage\settings\WebEngageDictionary;
use modules\webEngage\src\service\WebEngageRequestService;
use sales\helpers\app\AppHelper;
use sales\helpers\setting\SettingHelper;
use sales\model\clientData\entity\ClientData;
use sales\model\clientData\entity\ClientDataQuery;
use sales\model\clientDataKey\entity\ClientDataKeyDictionary;
use sales\model\clientDataKey\service\ClientDataKeyService;
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
        $keyId = ClientDataKeyService::getIdByKeyCache(ClientDataKeyDictionary::APP_CALL_OUT_TOTAL_COUNT);
        if ($keyId) {
            ClientDataQuery::createOrIncrementValue($this->clientId, $keyId, new \DateTimeImmutable());
        }

        $call = Call::findOne($this->callId);

        if ($call) {
            try {
                $clientData = ClientData::findOne(['cd_key_id' => $keyId, 'cd_client_id' => $this->clientId]);

                if ($clientData && $clientData->cd_field_value <= 1 && $call->isStatusNoAnswer()) {
                    $data = [
                        'anonymousId' => (string) $this->clientId,
                        'eventName' => WebEngageDictionary::EVENT_CALL_FIRST_CALL_NOT_PICKED,
                        'eventTime' => date('Y-m-d\TH:i:sO')
                    ];
                    $webEngageEventForm = new WebEngageEventForm();
                    if (!$webEngageEventForm->load($data)) {
                        throw new \RuntimeException('WebEngageEventForm not loaded');
                    }
                    $webEngageRequestService = new WebEngageRequestService();
                    $webEngageRequestService->addEvent($webEngageEventForm);
                }

                if ($call->c_call_duration > SettingHelper::getUserPrickedCallDuration() && $call->isStatusCompleted()) {
                    $data = [
                        'anonymousId' => (string) $this->clientId,
                        'eventName' => WebEngageDictionary::EVENT_CALL_USER_PICKED_CALL,
                        'eventTime' => date('Y-m-d\TH:i:sO')
                    ];
                    $webEngageEventForm = new WebEngageEventForm();
                    if (!$webEngageEventForm->load($data)) {
                        throw new \RuntimeException('WebEngageEventForm not loaded');
                    }
                    $webEngageRequestService = new WebEngageRequestService();
                    $webEngageRequestService->addEvent($webEngageEventForm);
                }
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e, true), 'error:CallOutEndedJob:Throwable');
            }
        }
    }
}
