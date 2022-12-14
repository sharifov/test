<?php

namespace modules\flight\src\useCases\voluntaryExchangeManualCreate\service;

use common\components\BackOffice;

/**
 * Class VoluntaryExchangeBOService
 *
 * @property VoluntaryExchangeBOPrepareService $BOPrepareService
 * @property array|null $result
 */
class VoluntaryExchangeBOService
{
    private VoluntaryExchangeBOPrepareService $BOPrepareService;
    private ?array $result = null;

    /**
     * @param VoluntaryExchangeBOPrepareService $voluntaryExchangeBOPrepareService
     */
    public function __construct(VoluntaryExchangeBOPrepareService $voluntaryExchangeBOPrepareService)
    {
        $this->BOPrepareService = $voluntaryExchangeBOPrepareService;
    }

    public function requestProcessing(): VoluntaryExchangeBOService
    {
        if (empty($this->BOPrepareService->getBookingId())) {
            throw new \RuntimeException('BookingId is empty. Request to BO "getExchangeData" not send');
        }
        if (empty($this->BOPrepareService->getApiKey())) {
            throw new \RuntimeException('ApiKey is empty. Request to BO "getExchangeData" not send');
        }

        $data['apiKey'] = $this->BOPrepareService->getApiKey();
        $data['bookingId'] = $this->BOPrepareService->getBookingId();
//        $data['tickets'] = $this->BOPrepareService->getTickets();

        $this->result = BackOffice::getExchangeData($data);
        return $this;
    }

    public function isAllow(): ?bool
    {
        $allow = $this->result['allow'] ?? null;
        if ($allow === null) {
            return null;
        }
        return (bool) $allow;
    }

    public function getResult(): ?array
    {
        return $this->result;
    }

    public function getServiceFeeAmount(): ?float
    {
        if ($amount = $this->result['exchange']['customerPackage']['serviceFee']['amount'] ?? null) {
            return self::prepareFloat($amount);
        }
        return null;
    }

    /**
     * @param $value
     * @return float
     */
    public static function prepareFloat($value): float
    {
        return (float) preg_replace("/[^-0-9.]/", '', $value);
    }

    public function getServiceFeeCurrency(): ?string
    {
        return $this->result['exchange']['customerPackage']['serviceFee']['currency'] ?? null;
    }

    public function getCustomerPackage(bool $asSerialize = true): ?string
    {
        if (($customerPackage = $this->result['exchange']['customerPackage'] ?? null) && $asSerialize) {
            return serialize($customerPackage);
        }
        return $customerPackage;
    }
}
