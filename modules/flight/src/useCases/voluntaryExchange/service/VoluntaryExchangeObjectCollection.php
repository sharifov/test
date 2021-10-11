<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use modules\flight\src\repositories\flightRequest\FlightRequestRepository;
use modules\flight\src\repositories\flightRequestLog\FlightRequestLogRepository;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesSaleService;
use sales\services\client\ClientManageService;
use Yii;

/**
 * Class VoluntaryExchangeObjectCollection
 *
 */
class VoluntaryExchangeObjectCollection
{
    private ?CasesRepository $casesRepository;
    private ?FlightRequestRepository $flightRequestRepository;
    private ?FlightRequestLogRepository $flightRequestLogRepository;
    private ?ClientManageService $clientManageService;
    private ?CasesSaleService $casesSaleService;

    public function getCasesSaleService(): CasesSaleService
    {
        return $this->casesSaleService ?? ($this->casesSaleService = Yii::createObject(
            CasesSaleService::class
        ));
    }

    public function getClientManageService(): ClientManageService
    {
        return $this->clientManageService ?? ($this->clientManageService = Yii::createObject(
            ClientManageService::class
        ));
    }

    public function getCasesRepository(): CasesRepository
    {
        return $this->casesRepository ?? ($this->casesRepository = Yii::createObject(
            CasesRepository::class
        ));
    }

    public function getFlightRequestRepository(): FlightRequestRepository
    {
        return $this->flightRequestRepository ?? ($this->flightRequestRepository = Yii::createObject(
            FlightRequestRepository::class
        ));
    }

    public function getFlightRequestLogRepository(): FlightRequestLogRepository
    {
        return $this->flightRequestLogRepository ?? ($this->flightRequestLogRepository = Yii::createObject(
            FlightRequestLogRepository::class
        ));
    }
}
