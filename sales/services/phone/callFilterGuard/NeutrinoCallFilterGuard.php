<?php

namespace sales\services\phone\callFilterGuard;

use frontend\helpers\JsonHelper;
use sales\model\contactPhoneList\service\ContactPhoneListService;
use sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use sales\model\contactPhoneServiceInfo\service\ContactPhoneInfoService;
use sales\services\phone\checkPhone\CheckPhoneNeutrinoService;

/**
 * Class NeutrinoCallFilterGuard
 *
 * @property string $phone
 * @property int $trustPercent
 * @property array|null $response
 */
class NeutrinoCallFilterGuard implements CheckServiceInterface
{
    private string $phone;
    private int $trustPercent = 0;
    private $response;

    public function __construct(string $phone)
    {
        $this->phone = $phone;
    }

    public function default(): CheckServiceInterface
    {
        $this->response = $this->getResponseData();

        $this->trustPercent = 100;
        return $this;
    }

    public function getResponseData(): array
    {
        if ($responseData = $this->getLocalResponseData()) {
            return $responseData;
        }

        if (!$apiResponseData = $this->getApiResponseData()) {
            throw new \DomainException('ApiResponseData cannot be empty');
        }
        $contactPhoneList = ContactPhoneListService::getOrCreate($this->phone);
        ContactPhoneInfoService::getOrCreate(
            $contactPhoneList->cpl_id,
            ContactPhoneServiceInfo::SERVICE_NEUTRINO,
            $apiResponseData
        );
        return $apiResponseData;
    }

    public function getLocalResponseData(): ?array
    {
        $contactPhoneServiceInfo = ContactPhoneInfoService::findByPhoneAndService(
            $this->getPhone(),
            ContactPhoneServiceInfo::SERVICE_NEUTRINO
        );
        if (!$contactPhoneServiceInfo || empty($contactPhoneServiceInfo->cpsi_data_json)) {
            return null;
        }
        return JsonHelper::decode($contactPhoneServiceInfo->cpsi_data_json);
    }

    public function getApiResponseData(): ?array
    {
        $checkPhoneNeutrinoService = new CheckPhoneNeutrinoService($this->phone);

        if ($numbers = $checkPhoneNeutrinoService->getRequest()) {
            return reset($numbers);
        }
        return null;
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
