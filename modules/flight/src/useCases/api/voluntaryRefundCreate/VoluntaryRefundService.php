<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

use common\models\CaseSale;
use common\models\Client;
use modules\flight\src\useCases\sale\FlightFromSaleService;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleService;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeQuery;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundQuery;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\entities\cases\CasesQuery;
use sales\exception\BoResponseException;
use sales\exception\ValidationException;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\helpers\ErrorsToStringHelper;
use sales\helpers\setting\SettingHelper;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesCreateService;
use sales\services\cases\CasesSaleService;
use sales\services\client\ClientCreateForm;
use sales\services\client\ClientManageService;

/**
 * Class VoluntaryRefundService
 * @package modules\flight\src\useCases\api\voluntaryRefundCreate
 *
 * @property CasesSaleService $casesSaleService
 * @property ClientManageService $clientManageService
 * @property OrderCreateFromSaleService $orderCreateFromSaleService
 * @property OrderRepository $orderRepository
 * @property FlightFromSaleService $flightFromSaleService
 * @property CasesCreateService $casesCreateService
 * @property CasesRepository $casesRepository
 * @property ProductQuoteRefundRepository $productQuoteRefundRepository
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
 * @property ProductQuoteRepository $productQuoteRepository
 */
class VoluntaryRefundService
{
    private const CASE_CREATE_CATEGORY_KEY = 'voluntary_refund';

    private CasesSaleService $casesSaleService;
    private ClientManageService $clientManageService;
    private OrderCreateFromSaleService $orderCreateFromSaleService;
    private OrderRepository $orderRepository;
    private FlightFromSaleService $flightFromSaleService;
    private CasesCreateService $casesCreateService;
    private CasesRepository $casesRepository;
    private ProductQuoteRefundRepository $productQuoteRefundRepository;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;
    private ProductQuoteRepository $productQuoteRepository;

    public function __construct(
        CasesSaleService $casesSaleService,
        ClientManageService $clientManageService,
        OrderCreateFromSaleService $orderCreateFromSaleService,
        OrderRepository $orderRepository,
        FlightFromSaleService $flightFromSaleService,
        CasesCreateService $casesCreateService,
        CasesRepository $casesRepository,
        ProductQuoteRefundRepository $productQuoteRefundRepository,
        ProductQuoteChangeRepository $productQuoteChangeRepository,
        ProductQuoteRepository $productQuoteRepository
    ) {
        $this->casesSaleService = $casesSaleService;
        $this->clientManageService = $clientManageService;
        $this->orderCreateFromSaleService = $orderCreateFromSaleService;
        $this->orderRepository = $orderRepository;
        $this->flightFromSaleService = $flightFromSaleService;
        $this->casesCreateService = $casesCreateService;
        $this->casesRepository = $casesRepository;
        $this->productQuoteRefundRepository = $productQuoteRefundRepository;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
        $this->productQuoteRepository = $productQuoteRepository;
    }

    public function startRefundAutoProcess(VoluntaryRefundCreateForm $voluntaryRefundCreateForm, int $projectId, ?int $originProductQuoteId): void
    {
        try {
            $caseCategoryKey = self::getCategoryKey();
            if (!$case = CasesQuery::getLastActiveCaseByBookingId($voluntaryRefundCreateForm->booking_id, $caseCategoryKey)) {
                $case = $this->casesCreateService->createRefund(
                    $voluntaryRefundCreateForm->booking_id,
                    $projectId,
                    $caseCategoryKey
                );
            }
        } catch (\Throwable $e) {
            throw new VoluntaryRefundException('Case creation Failed', VoluntaryRefundException::CASE_CREATION_FAILED);
        }

        try {
            $saleData = $this->getCaseSaleData($voluntaryRefundCreateForm->booking_id, $case, CaseEventLog::VOLUNTARY_REFUND_CREATE);

            $orderCreateSaleForm = new OrderCreateFromSaleForm();
            if (!$orderCreateSaleForm->load($saleData)) {
                throw new \DomainException('OrderCreateFromSaleForm not loaded');
            }
            if (!$orderCreateSaleForm->validate()) {
                throw new ValidationException(ErrorsToStringHelper::extractFromModel($orderCreateSaleForm));
            }
            $orderContactForm = OrderContactForm::fillForm($saleData);
            if (!$orderContactForm->validate()) {
                throw new ValidationException(ErrorsToStringHelper::extractFromModel($orderCreateSaleForm));
            }

            if ($caseSale = CaseSale::findOne(['css_cs_id' => $case->cs_id, 'css_sale_id' => $saleData['saleId']])) {
                $caseSale->delete();
            }
            $this->casesSaleService->createSaleByData($case->cs_id, $saleData);
            $case->addEventLog(
                CaseEventLog::VOLUNTARY_REFUND_CREATE,
                'Case Sale created by Data',
                ['case_id' => $case->cs_id]
            );
        } catch (\Throwable $e) {
            $this->errorHandler($case, null, 'Case Sale creation failed');
            throw new VoluntaryRefundException('Case Sale creation failed', VoluntaryRefundException::CASE_SALE_CREATION_FAILED);
        }

        try {
            $client = $this->getOrCreateClient(
                $projectId,
                $orderContactForm
            );
            $case->cs_client_id = $client->id;
            $this->casesRepository->save($case);
        } catch (\Throwable $e) {
            $this->errorHandler($case, null, 'Client creation failed');
            throw new VoluntaryRefundException('Client creation failed', VoluntaryRefundException::CLIENT_CREATION_FAILED);
        }

        try {
            $order = $this->createOrder(
                $orderCreateSaleForm,
                $orderContactForm,
                $case,
                $projectId
            );
        } catch (\Throwable $e) {
            $this->errorHandler($case, null, 'Order creation failed');
            throw new VoluntaryRefundException('Order creation failed', VoluntaryRefundException::ORDER_CREATION_FAILED);
        }

        try {
            if (!$originProductQuoteId) {
                $originProductQuote = $this->createOriginProductQuoteInfrastructure(
                    $orderCreateSaleForm,
                    $saleData,
                    $order,
                    $case
                );
                $originProductQuoteId = $originProductQuote->pq_id;
            }
        } catch (\Throwable $e) {
            $this->errorHandler($case, null, 'Origin Product Quote creation failed');
            throw new VoluntaryRefundException('Origin Product Quote creation failed', VoluntaryRefundException::ORIGIN_PRODUCT_QUOTE_CREATION_FAILED);
        }

        try {
            $totalCalculatedTickets = $this->calculateTotalTicketsAmount($voluntaryRefundCreateForm->refundForm->ticketForms);
            $productQuoteRefund = ProductQuoteRefund::createByVoluntaryRefund(
                1,
                $originProductQuoteId,
                0,
                $totalCalculatedTickets->processingFee,
                $totalCalculatedTickets->refundAmount,
                $totalCalculatedTickets->airlinePenalty,
                $voluntaryRefundCreateForm->refundForm->currency,
                $order->or_client_currency_rate,
                $totalCalculatedTickets->refundAmount,
                $totalCalculatedTickets->refundAmount,
                $case->cs_id
            );
            $this->productQuoteRefundRepository->save($productQuoteRefund);
        } catch (\Throwable $e) {
            $this->errorHandler($case, null, 'Product Quote Refund creation failed');
            throw new VoluntaryRefundException('Product Quote Refund creation failed', VoluntaryRefundException::PRODUCT_QUOTE_REFUND_CREATION_FAILED);
        }

        $productQuoteRefund->processing();
        $this->productQuoteRefundRepository->save($productQuoteRefund);

        $case->awaiting(null, 'Product Quote Refund initiated');
        $this->casesRepository->save($case);
    }

    public function processProductQuote(ProductQuote $productQuote, VoluntaryRefundCreateForm $form, int $projectId): void
    {
        if ($productQuoteRefundsNotFinished = ProductQuoteRefundQuery::findAllNotFinishedByProductQuoteId($productQuote->pq_id)) {
            foreach ($productQuoteRefundsNotFinished as $productQuoteRefund) {
                $productQuoteRefund->cancel();
                $this->productQuoteRefundRepository->save($productQuoteRefund);
            }
        }

        if ($productQuoteChangesNotFinished = ProductQuoteChangeQuery::findAllNotFinishedByProductQuoteId($productQuote->pq_id)) {
            foreach ($productQuoteChangesNotFinished as $productQuoteChange) {
                $productQuoteChange->declined();
                $this->productQuoteChangeRepository->save($productQuoteChange);
            }
        }

        $relatedProductQuotes = ProductQuoteQuery::getRelatedQuoteByOriginAndTypes($productQuote->pq_id, [ProductQuoteRelation::TYPE_REPROTECTION, ProductQuoteRelation::TYPE_VOLUNTARY_EXCHANGE]);
        foreach ($relatedProductQuotes as $relatedProductQuote) {
            $relatedProductQuote->declined();
            $this->productQuoteRepository->save($relatedProductQuote);
        }

        $this->startRefundAutoProcess($form, $projectId, $productQuote->pq_id);
    }

    private function getCaseSaleData(string $bookingId, Cases $case, int $caseEventLogType): array
    {
        $case->addEventLog(
            $caseEventLogType,
            'START: Request getSaleFrom BackOffice, BookingID: ' . $bookingId,
            ['fr_booking_id' => $bookingId]
        );
        $saleSearch = $this->casesSaleService->getSaleData($bookingId);
        if (empty($saleSearch['saleId'])) {
            throw new BoResponseException('Sale not found by Booking ID(' . $bookingId . ') from "cs/search"');
        }
        $case->addEventLog(
            $caseEventLogType,
            'START: Request DetailRequestToBackOffice SaleID: ' . $saleSearch['saleId'],
            ['sale_id' => $saleSearch['saleId']]
        );
        $saleData = $this->casesSaleService->detailRequestToBackOffice($saleSearch['saleId'], 0, 120, 1);
        $case->addEventLog($caseEventLogType, 'Responses from BackOffice accepted successfully');

        return $saleData;
    }

    private function getOrCreateClient(int $projectId, OrderContactForm $orderContactForm): Client
    {
        $clientForm = new ClientCreateForm();
        $clientForm->projectId = $projectId;
        $clientForm->typeCreate = Client::TYPE_CREATE_CASE;
        $clientForm->firstName = $orderContactForm->first_name;
        $clientForm->lastName = $orderContactForm->last_name;

        return $this->clientManageService->getOrCreate(
            [new PhoneCreateForm(['phone' => $orderContactForm->phone_number])],
            [new EmailCreateForm(['email' => $orderContactForm->email])],
            $clientForm
        );
    }

    private function createOrder(OrderCreateFromSaleForm $orderCreateFromSaleForm, OrderContactForm $orderContactForm, Cases $case, int $projectId): \modules\order\src\entities\order\Order
    {
        $order = $this->orderCreateFromSaleService->orderCreate($orderCreateFromSaleForm);
        if (!$order->validate()) {
            throw new \DomainException(ErrorsToStringHelper::extractFromModel($order));
        }
        $order->or_project_id = $projectId;
        $orderId = $this->orderRepository->save($order);

        $this->orderCreateFromSaleService->caseOrderRelation($orderId, $case->cs_id);
        $this->orderCreateFromSaleService->orderContactCreate($order, $orderContactForm);

        $case->addEventLog(
            CaseEventLog::VOLUNTARY_REFUND_CREATE,
            'Order created GID: ' . $order->or_gid,
            ['order_gid' => $order->or_gid]
        );

        return $order;
    }

    private function createOriginProductQuoteInfrastructure(
        OrderCreateFromSaleForm $orderCreateFromSaleForm,
        array $saleData,
        Order $order,
        Cases $case
    ): ProductQuote {
        $originProductQuote = $this->flightFromSaleService->createHandler($order, $orderCreateFromSaleForm, $saleData);
        $case->addEventLog(
            CaseEventLog::VOLUNTARY_REFUND_CREATE,
            'Origin ProductQuote created GID: ' . $originProductQuote->pq_gid,
            ['pq_gid' => $originProductQuote->pq_gid]
        );
        return $originProductQuote;
    }

    /**
     * @param TicketForm[] $ticketForms
     * @return TotalTicketCalculatedValuesDTO
     */
    private function calculateTotalTicketsAmount(array $ticketForms): TotalTicketCalculatedValuesDTO
    {
        $dto = new TotalTicketCalculatedValuesDTO();
        foreach ($ticketForms as $ticketForm) {
            $dto->processingFee += $ticketForm->processingFee;
            $dto->airlinePenalty += $ticketForm->airlinePenalty;
            $dto->refundAmount += $ticketForm->refundAmount;
        }
        return $dto;
    }

    private static function getCategoryKey(): string
    {
        if (!empty(SettingHelper::getVoluntaryRefundCaseCategory())) {
            return SettingHelper::getVoluntaryRefundCaseCategory();
        }
        return self::CASE_CREATE_CATEGORY_KEY;
    }

    private function errorHandler(?Cases $case, ?ProductQuoteRefund $productQuoteRefund, string $description): void
    {
        if ($case) {
            $case->addEventLog(CaseEventLog::CASE_AUTO_PROCESSING_MARK, $description);
            $case->offIsAutomate()->error(null, $description);
            $this->casesRepository->save($case);
        }

        if ($productQuoteRefund) {
            $productQuoteRefund->error();
            $this->productQuoteRefundRepository->save($productQuoteRefund);
        }
    }
}
