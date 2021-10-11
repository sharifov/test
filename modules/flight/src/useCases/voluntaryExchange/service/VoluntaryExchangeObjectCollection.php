<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use modules\flight\src\repositories\flightRequest\FlightRequestRepository;
use modules\flight\src\repositories\flightRequestLog\FlightRequestLogRepository;
use modules\flight\src\useCases\sale\FlightFromSaleService;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\services\createFromSale\OrderCreateFromSaleService;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteData\service\ProductQuoteDataManageService;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesSaleService;
use sales\services\client\ClientManageService;
use sales\repositories\product\ProductQuoteRepository;
use Yii;

/**
 * Class VoluntaryExchangeObjectCollection
 */
class VoluntaryExchangeObjectCollection
{
    private ?CasesRepository $casesRepository;
    private ?FlightRequestRepository $flightRequestRepository;
    private ?FlightRequestLogRepository $flightRequestLogRepository;
    private ?ClientManageService $clientManageService;
    private ?CasesSaleService $casesSaleService;
    private ?OrderCreateFromSaleService $orderCreateFromSaleService;
    private ?OrderRepository $orderRepository;
    private ?FlightFromSaleService $flightFromSaleService;
    private ?ProductQuoteChangeRepository $productQuoteChangeRepository;
    private ?ProductQuoteRepository $productQuoteRepository;
    private ?ProductQuoteDataManageService $productQuoteDataManageService;

    public function getProductQuoteDataManageService(): ProductQuoteDataManageService
    {
        return $this->productQuoteDataManageService ?? ($this->productQuoteDataManageService = Yii::createObject(
            ProductQuoteDataManageService::class
        ));
    }

    public function getProductQuoteRepository(): ProductQuoteRepository
    {
        return $this->productQuoteRepository ?? ($this->productQuoteRepository = Yii::createObject(
            ProductQuoteRepository::class
        ));
    }

    public function getProductQuoteChangeRepository(): ProductQuoteChangeRepository
    {
        return $this->productQuoteChangeRepository ?? ($this->productQuoteChangeRepository = Yii::createObject(
            ProductQuoteChangeRepository::class
        ));
    }

    public function getFlightFromSaleService(): FlightFromSaleService
    {
        return $this->flightFromSaleService ?? ($this->flightFromSaleService = Yii::createObject(
            FlightFromSaleService::class
        ));
    }

    public function getOrderRepository(): OrderRepository
    {
        return $this->orderRepository ?? ($this->orderRepository = Yii::createObject(
            OrderRepository::class
        ));
    }

    public function getOrderCreateFromSaleService(): OrderCreateFromSaleService
    {
        return $this->orderCreateFromSaleService ?? ($this->orderCreateFromSaleService = Yii::createObject(
            OrderCreateFromSaleService::class
        ));
    }

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