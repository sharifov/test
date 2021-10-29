<?php

namespace modules\flight\src\useCases\voluntaryExchange\service;

use modules\flight\src\repositories\flightQuotePaxPriceRepository\FlightQuotePaxPriceRepository;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\repositories\flightQuoteSegment\FlightQuoteSegmentRepository;
use modules\flight\src\repositories\FlightQuoteSegmentPaxBaggageChargeRepository\FlightQuoteSegmentPaxBaggageChargeRepository;
use modules\flight\src\repositories\flightQuoteSegmentPaxBaggageRepository\FlightQuoteSegmentPaxBaggageRepository;
use modules\flight\src\repositories\flightQuoteStatusLogRepository\FlightQuoteStatusLogRepository;
use modules\flight\src\repositories\flightQuoteTripRepository\FlightQuoteTripRepository;
use modules\flight\src\repositories\flightRequest\FlightRequestRepository;
use modules\flight\src\repositories\flightRequestLog\FlightRequestLogRepository;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\flight\src\useCases\sale\FlightFromSaleService;
use modules\invoice\src\entities\invoice\InvoiceRepository;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\services\createFromSale\OrderCreateFromSaleService;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteData\service\ProductQuoteDataManageService;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionRepository;
use modules\product\src\repositories\ProductQuoteRelationRepository;
use sales\repositories\cases\CasesRepository;
use sales\repositories\creditCard\CreditCardRepository;
use sales\services\cases\CasesCommunicationService;
use sales\services\cases\CasesSaleService;
use sales\services\client\ClientManageService;
use sales\repositories\product\ProductQuoteRepository;
use webapi\src\services\payment\PaymentRequestVoluntaryService;
use Yii;

/**
 * Class VoluntaryExchangeObjectCollection
 */
class VoluntaryExchangeObjectCollection
{
    private CasesRepository $casesRepository;
    private FlightRequestRepository $flightRequestRepository;
    private FlightRequestLogRepository $flightRequestLogRepository;
    private ClientManageService $clientManageService;
    private CasesSaleService $casesSaleService;
    private OrderCreateFromSaleService $orderCreateFromSaleService;
    private OrderRepository $orderRepository;
    private FlightFromSaleService $flightFromSaleService;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private ProductQuoteRepository $productQuoteRepository;
    private ProductQuoteDataManageService $productQuoteDataManageService;
    private CasesCommunicationService $casesCommunicationService;
    private InvoiceRepository $invoiceRepository;
    private CreditCardRepository $creditCardRepository;
    private FlightQuoteRepository $flightQuoteRepository;
    private FlightQuoteStatusLogRepository $flightQuoteStatusLogRepository;
    private FlightQuotePaxPriceRepository $flightQuotePaxPriceRepository;
    private FlightQuoteManageService $flightQuoteManageService;
    private ProductQuoteRelationRepository $productQuoteRelationRepository;
    private FlightQuoteTripRepository $flightQuoteTripRepository;
    private FlightQuoteSegmentRepository $flightQuoteSegmentRepository;
    private FlightQuoteSegmentPaxBaggageChargeRepository $flightQuoteSegmentPaxBaggageChargeRepository;
    private FlightQuoteSegmentPaxBaggageRepository $flightQuoteSegmentPaxBaggageRepository;
    private ProductQuoteOptionRepository $productQuoteOptionRepository;
    private BoRequestVoluntaryExchangeService $boRequestVoluntaryExchangeService;
    private PaymentRequestVoluntaryService $paymentRequestVoluntaryService;

    public function getPaymentRequestVoluntaryService(): PaymentRequestVoluntaryService
    {
        return $this->paymentRequestVoluntaryService ?? ($this->paymentRequestVoluntaryService = Yii::createObject(
            PaymentRequestVoluntaryService::class
        ));
    }

    public function getBoRequestVoluntaryExchangeService(): BoRequestVoluntaryExchangeService
    {
        return $this->boRequestVoluntaryExchangeService ?? ($this->boRequestVoluntaryExchangeService = Yii::createObject(
            BoRequestVoluntaryExchangeService::class
        ));
    }

    public function getProductQuoteOptionRepository(): ProductQuoteOptionRepository
    {
        return $this->productQuoteOptionRepository ?? ($this->productQuoteOptionRepository = Yii::createObject(
            ProductQuoteOptionRepository::class
        ));
    }

    public function getFlightQuoteSegmentPaxBaggageRepository(): FlightQuoteSegmentPaxBaggageRepository
    {
        return $this->flightQuoteSegmentPaxBaggageRepository ?? ($this->flightQuoteSegmentPaxBaggageRepository = Yii::createObject(
            FlightQuoteSegmentPaxBaggageRepository::class
        ));
    }

    public function getFlightQuoteSegmentPaxBaggageChargeRepository(): FlightQuoteSegmentPaxBaggageChargeRepository
    {
        return $this->flightQuoteSegmentPaxBaggageChargeRepository ?? ($this->flightQuoteSegmentPaxBaggageChargeRepository = Yii::createObject(
            FlightQuoteSegmentPaxBaggageChargeRepository::class
        ));
    }

    public function getFlightQuoteSegmentRepository(): FlightQuoteSegmentRepository
    {
        return $this->flightQuoteSegmentRepository ?? ($this->flightQuoteSegmentRepository = Yii::createObject(
            FlightQuoteSegmentRepository::class
        ));
    }

    public function getFlightQuoteTripRepository(): FlightQuoteTripRepository
    {
        return $this->flightQuoteTripRepository ?? ($this->flightQuoteTripRepository = Yii::createObject(
            FlightQuoteTripRepository::class
        ));
    }

    public function getProductQuoteRelationRepository(): ProductQuoteRelationRepository
    {
        return $this->productQuoteRelationRepository ?? ($this->productQuoteRelationRepository = Yii::createObject(
            ProductQuoteRelationRepository::class
        ));
    }

    public function getFlightQuoteManageService(): FlightQuoteManageService
    {
        return $this->flightQuoteManageService ?? ($this->flightQuoteManageService = Yii::createObject(
            FlightQuoteManageService::class
        ));
    }

    public function getFlightQuotePaxPriceRepository(): FlightQuotePaxPriceRepository
    {
        return $this->flightQuotePaxPriceRepository ?? ($this->flightQuotePaxPriceRepository = Yii::createObject(
            FlightQuotePaxPriceRepository::class
        ));
    }

    public function getFlightQuoteStatusLogRepository(): FlightQuoteStatusLogRepository
    {
        return $this->flightQuoteStatusLogRepository ?? ($this->flightQuoteStatusLogRepository = Yii::createObject(
            FlightQuoteStatusLogRepository::class
        ));
    }

    public function getFlightQuoteRepository(): FlightQuoteRepository
    {
        return $this->flightQuoteRepository ?? ($this->flightQuoteRepository = Yii::createObject(
            FlightQuoteRepository::class
        ));
    }

    public function getCreditCardRepository(): CreditCardRepository
    {
        return $this->creditCardRepository ?? ($this->creditCardRepository = Yii::createObject(
            CreditCardRepository::class
        ));
    }

    public function getInvoiceRepository(): InvoiceRepository
    {
        return $this->invoiceRepository ?? ($this->invoiceRepository = Yii::createObject(
            InvoiceRepository::class
        ));
    }

    public function getCasesCommunicationService(): CasesCommunicationService
    {
        return $this->casesCommunicationService ?? ($this->casesCommunicationService = Yii::createObject(
            CasesCommunicationService::class
        ));
    }

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
