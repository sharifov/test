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

        $this->requestProcessing();
    }

    private function requestProcessing(): void
    {
        $data['apiKey'] = $this->BOPrepareService->getApiKey();
        $data['bookingId'] = $this->BOPrepareService->getBookingId();
        $data['tickets'] = $this->BOPrepareService->getTickets();

        $this->result = BackOffice::getExchangeData($data);
    }

    public function isAllow(): bool
    {
        return (bool) $this->result['allow'];
    }

    public function getResult(): ?array
    {
        return $this->result;
    }
}
