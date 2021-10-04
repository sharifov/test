<?php

namespace sales\services\phone\callFilterGuard;

use frontend\helpers\JsonHelper;
use sales\model\contactPhoneList\service\ContactPhoneListService;
use sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use sales\model\contactPhoneServiceInfo\service\ContactPhoneInfoService;
use yii\helpers\ArrayHelper;

/**
 * Class TwilioCallFilterGuard
 *
 * @property string $phone
 * @property int $trustPercent
 * @property array|null $response
 */
class TwilioCallFilterGuard implements CheckServiceInterface
{
    private string $phone;
    private int $trustPercent = 0;
    public $response;

    public function __construct(string $phone)
    {
        $this->phone = $phone;
    }

    public function default(): CheckServiceInterface
    {
        $this->response = $this->getResponseData();
        $type = ArrayHelper::getValue($this->response, 'result.result.carrier.type');

        if ($type === 'voip' || $type === null || $type === 'null') {
            $this->trustPercent = 0;
        } else {
            $this->trustPercent = 100;
        }
        return $this;
    }

    public function getResponseData(): array
    {
        if ($responseData = $this->getLocalResponseData()) {
            return $responseData;
        }

        $apiResponseData = $this->getApiResponseData();
        $contactPhoneList = ContactPhoneListService::getOrCreate($this->phone);
        ContactPhoneInfoService::getOrCreate(
            $contactPhoneList->cpl_id,
            ContactPhoneServiceInfo::SERVICE_TWILIO,
            $apiResponseData
        );
        return $apiResponseData;
    }

    public function getLocalResponseData(): ?array
    {
        $contactPhoneServiceInfo = ContactPhoneInfoService::findByPhoneAndService(
            $this->getPhone(),
            ContactPhoneServiceInfo::SERVICE_TWILIO
        );
        if (!$contactPhoneServiceInfo || empty($contactPhoneServiceInfo->cpsi_data_json)) {
            return null;
        }
        return JsonHelper::decode($contactPhoneServiceInfo->cpsi_data_json);
    }

    public function getApiResponseData(): array
    {
        return \Yii::$app->communication->twilioLookup($this->phone);
    }

    public function getTrustPercent(): int
    {
        return $this->trustPercent;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }
}
