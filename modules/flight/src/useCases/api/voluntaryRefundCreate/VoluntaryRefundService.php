<?php

namespace modules\flight\src\useCases\api\voluntaryRefundCreate;

use common\components\BackOffice;
use common\models\CaseSale;
use common\models\Client;
use common\models\Project;
use modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefund;
use modules\flight\src\entities\flightQuoteTicketRefund\FlightQuoteTicketRefundRepository;
use modules\flight\src\useCases\api\voluntaryRefundCreate\dto\RefundCreateResultDto;
use modules\flight\src\useCases\sale\FlightFromSaleService;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\orderRefund\OrderRefund;
use modules\order\src\entities\orderRefund\OrderRefundRepository;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleService;
use modules\order\src\services\OrderManageService;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeQuery;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefund;
use modules\product\src\entities\productQuoteObjectRefund\ProductQuoteObjectRefundRepository;
use modules\product\src\entities\productQuoteOption\ProductQuoteOptionsQuery;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefund;
use modules\product\src\entities\productQuoteOptionRefund\ProductQuoteOptionRefundRepository;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundQuery;
use modules\product\src\entities\productQuoteRefund\ProductQuoteRefundRepository;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use src\entities\cases\CaseEventLog;
use src\entities\cases\Cases;
use src\entities\cases\CasesQuery;
use src\exception\BoResponseException;
use src\exception\ValidationException;
use src\forms\lead\EmailCreateForm;
use src\forms\lead\PhoneCreateForm;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\helpers\setting\SettingHelper;
use src\repositories\cases\CasesRepository;
use src\services\cases\CasesCreateService;
use src\services\cases\CasesSaleService;
use src\services\client\ClientCreateForm;
use src\services\client\ClientManageService;
use src\services\CurrencyHelper;
use webapi\src\request\BoRequestDataHelper;
use webapi\src\services\payment\BillingInfoApiVoluntaryService;
use webapi\src\services\payment\PaymentRequestVoluntaryService;

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
 * @property OrderRefundRepository $orderRefundRepository
 * @property FlightQuoteTicketRefundRepository $flightQuoteTicketRefundRepository
 * @property ProductQuoteObjectRefundRepository $productQuoteObjectRefundRepository
 * @property ProductQuoteOptionRefundRepository $productQuoteOptionRefundRepository
 * @property PaymentRequestVoluntaryService $paymentRequestVoluntaryService
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
    private OrderRefundRepository $orderRefundRepository;
    private FlightQuoteTicketRefundRepository $flightQuoteTicketRefundRepository;
    private ProductQuoteObjectRefundRepository $productQuoteObjectRefundRepository;
    private ProductQuoteOptionRefundRepository $productQuoteOptionRefundRepository;
    private PaymentRequestVoluntaryService $paymentRequestVoluntaryService;

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
        ProductQuoteRepository $productQuoteRepository,
        OrderRefundRepository $orderRefundRepository,
        FlightQuoteTicketRefundRepository $flightQuoteTicketRefundRepository,
        ProductQuoteObjectRefundRepository $productQuoteObjectRefundRepository,
        ProductQuoteOptionRefundRepository $productQuoteOptionRefundRepository,
        PaymentRequestVoluntaryService $paymentRequestVoluntaryService
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
        $this->orderRefundRepository = $orderRefundRepository;
        $this->flightQuoteTicketRefundRepository = $flightQuoteTicketRefundRepository;
        $this->productQuoteObjectRefundRepository = $productQuoteObjectRefundRepository;
        $this->productQuoteOptionRefundRepository = $productQuoteOptionRefundRepository;
        $this->paymentRequestVoluntaryService = $paymentRequestVoluntaryService;
    }

    public function startRefundAutoProcess(VoluntaryRefundCreateForm $voluntaryRefundCreateForm, Project $project, ?ProductQuote $originProductQuote): RefundCreateResultDto
    {
        try {
            $caseCategoryKey = self::getCaseCategoryKey();
            if (!$case = CasesQuery::getLastActiveCaseByBookingId($voluntaryRefundCreateForm->bookingId, $caseCategoryKey)) {
                $case = $this->casesCreateService->createRefund(
                    $voluntaryRefundCreateForm->bookingId,
                    $project->id,
                    $caseCategoryKey
                );
            }
        } catch (\Throwable $e) {
            $this->errorHandler(null, null, 'Case Sale creation failed', $e);
            throw new VoluntaryRefundCodeException('Case creation Failed', VoluntaryRefundCodeException::CASE_CREATION_FAILED);
        }

        try {
            $saleData = $this->getCaseSaleData($voluntaryRefundCreateForm->bookingId, $case, CaseEventLog::VOLUNTARY_REFUND_CREATE);

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
                ['case_id' => $case->cs_id],
                CaseEventLog::CATEGORY_INFO
            );
        } catch (\Throwable $e) {
            $this->errorHandler($case, null, 'Case Sale creation failed', $e);
            throw new VoluntaryRefundCodeException('Case Sale creation failed', VoluntaryRefundCodeException::CASE_SALE_CREATION_FAILED);
        }

        try {
            $client = $this->getOrCreateClient(
                $project->id,
                $orderContactForm
            );
            $case->cs_client_id = $client->id;
            $this->casesRepository->save($case);
        } catch (\Throwable $e) {
            $this->errorHandler($case, null, 'Client creation failed', $e);
            throw new VoluntaryRefundCodeException('Client creation failed', VoluntaryRefundCodeException::CLIENT_CREATION_FAILED);
        }

        try {
            $bookingId = !empty($saleData['baseBookingId']) ? $saleData['baseBookingId'] : $saleData['bookingId'];
            if (
                (!$originProductQuote || !$order = $originProductQuote->pqOrder) ||
                (!$order = OrderManageService::getBySaleIdOrBookingId((int) $saleData['saleId'], $bookingId))
            ) {
                $order = $this->createOrder(
                    $orderCreateSaleForm,
                    $orderContactForm,
                    $case,
                    $project->id
                );
            } else {
                $this->orderCreateFromSaleService->caseOrderRelation($order->or_id, $case->cs_id);
                $this->orderCreateFromSaleService->orderContactCreate($order, $orderContactForm);

                $case->addEventLog(
                    CaseEventLog::VOLUNTARY_REFUND_CREATE,
                    'Order related with case GID: ' . $order->or_gid,
                    ['order_gid' => $order->or_gid],
                    CaseEventLog::CATEGORY_INFO
                );
            }

            $order->or_client_currency = $voluntaryRefundCreateForm->refundForm->currency;
            $order->or_client_currency_rate = $order->orClientCurrency->cur_app_rate;
            $this->orderRepository->save($order);
        } catch (\Throwable $e) {
            $this->errorHandler($case, null, 'Order creation failed', $e);
            throw new VoluntaryRefundCodeException('Order creation failed', VoluntaryRefundCodeException::ORDER_CREATION_FAILED);
        }

        try {
            if (!$originProductQuote) {
                $originProductQuote = $this->createOriginProductQuoteStructure(
                    $orderCreateSaleForm,
                    $saleData,
                    $order,
                    $case
                );
            }
        } catch (\Throwable $e) {
            $this->errorHandler($case, null, 'Origin Product Quote creation failed', $e);
            throw new VoluntaryRefundCodeException('Origin Product Quote creation failed', VoluntaryRefundCodeException::ORIGIN_PRODUCT_QUOTE_CREATION_FAILED);
        }

        if (!$boRequestEndpoint = SettingHelper::getVoluntaryRefundBoEndpoint()) {
            $this->errorHandler($case, null, 'BO endpoint is not set', null);
            throw new \RuntimeException('BO endpoint is not set', VoluntaryRefundCodeException::BO_REQUEST_IS_NO_SEND);
        }

        $boDataRequest = BoRequestDataHelper::getDataForVoluntaryCreateByForm($project->api_key, $voluntaryRefundCreateForm);
        $result = BackOffice::voluntaryRefund($boDataRequest, $boRequestEndpoint);
        if (mb_strtolower($result['status']) === 'failed') {
            $this->errorHandler($case, null, 'BO returns an error', null);
            throw new BoResponseException($result['message'] ?? '', VoluntaryRefundCodeException::BO_REQUEST_FAILED);
        }

        try {
            $orderRefund = OrderRefund::createByVoluntaryRefund(
                OrderRefund::generateUid(),
                $order->or_id,
                $order->or_app_total,
                CurrencyHelper::convertToBaseCurrency($voluntaryRefundCreateForm->refundForm->penaltyAmount, $order->orClientCurrency->cur_base_rate),
                CurrencyHelper::convertToBaseCurrency($voluntaryRefundCreateForm->refundForm->processingFee, $order->orClientCurrency->cur_base_rate),
                CurrencyHelper::convertToBaseCurrency($voluntaryRefundCreateForm->refundForm->totalRefundAmount, $order->orClientCurrency->cur_base_rate),
                $order->or_client_currency,
                $order->or_client_currency_rate,
                $voluntaryRefundCreateForm->refundForm->totalPaid,
                $voluntaryRefundCreateForm->refundForm->penaltyAmount,
                $voluntaryRefundCreateForm->refundForm->processingFee,
                $voluntaryRefundCreateForm->refundForm->totalRefundAmount,
                $case->cs_id
            );
            $this->orderRefundRepository->save($orderRefund);

            $productQuoteRefund = ProductQuoteRefund::createByVoluntaryRefund(
                $orderRefund->orr_id,
                $originProductQuote->pq_id,
                CurrencyHelper::convertToBaseCurrency($voluntaryRefundCreateForm->refundForm->totalPaid, $order->orClientCurrency->cur_base_rate),
                CurrencyHelper::convertToBaseCurrency($voluntaryRefundCreateForm->refundForm->processingFee, $order->orClientCurrency->cur_base_rate),
                CurrencyHelper::convertToBaseCurrency($voluntaryRefundCreateForm->refundForm->totalRefundAmount, $order->orClientCurrency->cur_base_rate),
                CurrencyHelper::convertToBaseCurrency($voluntaryRefundCreateForm->refundForm->penaltyAmount, $order->orClientCurrency->cur_base_rate),
                $voluntaryRefundCreateForm->refundForm->currency,
                $order->or_client_currency_rate,
                $voluntaryRefundCreateForm->refundForm->totalPaid,
                $voluntaryRefundCreateForm->refundForm->penaltyAmount,
                $voluntaryRefundCreateForm->refundForm->processingFee,
                $voluntaryRefundCreateForm->refundForm->totalRefundAmount,
                $case->cs_id,
                $voluntaryRefundCreateForm->refundForm->orderId,
                $voluntaryRefundCreateForm->toArray(),
                CurrencyHelper::convertToBaseCurrency($voluntaryRefundCreateForm->getPaymentRequestForm()->amount ?? 0, $order->orClientCurrency->cur_base_rate),
                $voluntaryRefundCreateForm->getPaymentRequestForm()->amount ?? 0,
                null
            );
            $productQuoteRefund->pending();
            $this->productQuoteRefundRepository->save($productQuoteRefund);

            foreach ($voluntaryRefundCreateForm->refundForm->ticketForms as $ticketForm) {
                $flightQuoteTicketRefund = FlightQuoteTicketRefund::create($ticketForm->number, null);
                $this->flightQuoteTicketRefundRepository->save($flightQuoteTicketRefund);

                $productQuoteObjectRefund = ProductQuoteObjectRefund::create(
                    $productQuoteRefund->pqr_id,
                    $flightQuoteTicketRefund->fqtr_id,
                    CurrencyHelper::convertToBaseCurrency($ticketForm->selling, $order->orClientCurrency->cur_base_rate),
                    CurrencyHelper::convertToBaseCurrency($ticketForm->airlinePenalty, $order->orClientCurrency->cur_base_rate),
                    CurrencyHelper::convertToBaseCurrency($ticketForm->processingFee, $order->orClientCurrency->cur_base_rate),
                    CurrencyHelper::convertToBaseCurrency($ticketForm->refundable, $order->orClientCurrency->cur_base_rate),
                    $voluntaryRefundCreateForm->refundForm->currency,
                    $order->or_client_currency_rate,
                    $ticketForm->selling,
                    $ticketForm->airlinePenalty,
                    $ticketForm->processingFee,
                    $ticketForm->refundable,
                    null,
                    $ticketForm->toArray()
                );
                $productQuoteObjectRefund->pending();
                $productQuoteObjectRefund->detachBehavior('user');
                $this->productQuoteObjectRefundRepository->save($productQuoteObjectRefund);
            }

            foreach ($voluntaryRefundCreateForm->refundForm->auxiliaryOptionsForms as $auxiliaryOptionsForm) {
                $productQuoteOption = ProductQuoteOptionsQuery::getByProductQuoteIdOptionKey($originProductQuote->pq_id, $auxiliaryOptionsForm->type);

                $productQuoteOptionRefund = ProductQuoteOptionRefund::create(
                    $orderRefund->orr_id,
                    $productQuoteRefund->pqr_id,
                    $productQuoteOption->pqo_id ?? null,
                    CurrencyHelper::convertToBaseCurrency($auxiliaryOptionsForm->amount, $order->orClientCurrency->cur_base_rate),
                    null,
                    null,
                    CurrencyHelper::convertToBaseCurrency($auxiliaryOptionsForm->refundable, $order->orClientCurrency->cur_base_rate),
                    $voluntaryRefundCreateForm->refundForm->currency,
                    $order->or_client_currency_rate,
                    $auxiliaryOptionsForm->amount,
                    $auxiliaryOptionsForm->refundable,
                    $auxiliaryOptionsForm->refundAllow,
                    $auxiliaryOptionsForm->toArray()
                );
                $productQuoteOptionRefund->pending();
                $productQuoteOptionRefund->detachBehavior('user');
                $this->productQuoteOptionRefundRepository->save($productQuoteOptionRefund);
            }
        } catch (\RuntimeException | \DomainException $e) {
            $this->errorHandler($case, $productQuoteRefund ?? null, 'Product Quote Refund structure creation failed: ' . $e->getMessage(), $e);
            return new RefundCreateResultDto($result['saleData'] ?? [], $result['refundData'] ?? []);
//            throw new VoluntaryRefundCodeException('Product Quote Refund structure creation failed', VoluntaryRefundCodeException::PRODUCT_QUOTE_REFUND_CREATION_FAILED);
        } catch (\Throwable $e) {
            $this->errorHandler($case, $productQuoteRefund ?? null, 'Product Quote Refund structure creation failed', $e);
            return new RefundCreateResultDto($result['saleData'] ?? [], $result['refundData'] ?? []);
//            throw new VoluntaryRefundCodeException('Product Quote Refund structure creation failed', VoluntaryRefundCodeException::PRODUCT_QUOTE_REFUND_CREATION_FAILED);
        }

        try {
            if ($voluntaryRefundCreateForm->paymentRequestForm) {
                $this->paymentRequestVoluntaryService->processing(
                    $voluntaryRefundCreateForm->paymentRequestForm,
                    $order,
                    'Create by Voluntary Refund API processing'
                );
            }
        } catch (\Throwable $e) {
            $this->errorHandler($case, $productQuoteRefund ?? null, 'PaymentRequest processing is failed', $e);
//            throw new VoluntaryRefundCodeException('PaymentRequest processing is failed', VoluntaryRefundCodeException::PAYMENT_DATA_PROCESSED_FAILED);
        }

        try {
            if ($voluntaryRefundCreateForm->billingInfoForm) {
                $paymentMethodId = $this->paymentRequestVoluntaryService->getPaymentMethod()->pm_id ?? null;
                $creditCardId = $this->paymentRequestVoluntaryService->getCreditCard()->cc_id ?? null;

                BillingInfoApiVoluntaryService::getOrCreateBillingInfo(
                    $voluntaryRefundCreateForm->billingInfoForm,
                    $order->getId(),
                    $creditCardId,
                    $paymentMethodId
                );
            }
        } catch (\Throwable $e) {
            $this->errorHandler($case, $productQuoteRefund ?? null, 'BillingInfo processing is failed', $e);
//            throw new VoluntaryRefundCodeException('BillingInfo processing is failed', VoluntaryRefundCodeException::BILLING_INFO_PROCESSED_FAILED);
        }

        if (isset($productQuoteRefund)) {
            $productQuoteRefund->inProcessing();
            $this->productQuoteRefundRepository->save($productQuoteRefund);
        }

        $case->awaiting(null, 'Product Quote Refund initiated');
        $this->casesRepository->save($case);

        return new RefundCreateResultDto($result['saleData'] ?? [], $result['refundData'] ?? []);
    }

    public function processProductQuote(ProductQuote $productQuote): self
    {
        if ($productQuoteRefundsNotFinished = ProductQuoteRefundQuery::findAllNotFinishedByProductQuoteId($productQuote->pq_id)) {
            foreach ($productQuoteRefundsNotFinished as $productQuoteRefund) {
                $productQuoteRefund->cancel();
                $productQuoteRefund->detachBehavior('user');
                $this->productQuoteRefundRepository->save($productQuoteRefund);
            }
        }

        if ($productQuoteChangesNotFinished = ProductQuoteChangeQuery::findAllNotFinishedByProductQuoteId($productQuote->pq_id)) {
            foreach ($productQuoteChangesNotFinished as $productQuoteChange) {
                $productQuoteChange->cancel();
                $this->productQuoteChangeRepository->save($productQuoteChange);
            }
        }

        $relatedProductQuotes = ProductQuoteQuery::getRelatedQuoteByOriginTypesStatuses(
            $productQuote->pq_id,
            [ProductQuoteRelation::TYPE_REPROTECTION, ProductQuoteRelation::TYPE_VOLUNTARY_EXCHANGE],
            ProductQuoteStatus::PROCESSING_LIST
        );
        foreach ($relatedProductQuotes as $relatedProductQuote) {
            $relatedProductQuote->declined();
            $this->productQuoteRepository->save($relatedProductQuote);
        }
        return $this;
    }

    private function getCaseSaleData(string $bookingId, Cases $case, int $caseEventLogType): array
    {
        $case->addEventLog(
            $caseEventLogType,
            'START: Request getSaleFrom BackOffice, BookingID: ' . $bookingId,
            ['fr_booking_id' => $bookingId],
            CaseEventLog::CATEGORY_INFO
        );
        $projectKey = $case->project->api_key ?? null;
        $saleSearch = $this->casesSaleService->getSaleData($projectKey, $bookingId);
        if (empty($saleSearch['saleId'])) {
            throw new BoResponseException('Sale not found by Booking ID(' . $bookingId . ') from "cs/search"');
        }
        $case->addEventLog(
            $caseEventLogType,
            'START: Request DetailRequestToBackOffice SaleID: ' . $saleSearch['saleId'],
            ['sale_id' => $saleSearch['saleId']],
            CaseEventLog::CATEGORY_INFO
        );
        $saleData = $this->casesSaleService->detailRequestToBackOffice($saleSearch['saleId'], 0, 120, 1);
        $case->addEventLog($caseEventLogType, 'Responses from BackOffice accepted successfully', [], CaseEventLog::CATEGORY_INFO);

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
            ['order_gid' => $order->or_gid],
            CaseEventLog::CATEGORY_INFO
        );

        return $order;
    }

    private function createOriginProductQuoteStructure(
        OrderCreateFromSaleForm $orderCreateFromSaleForm,
        array $saleData,
        Order $order,
        Cases $case
    ): ProductQuote {
        $originProductQuote = $this->flightFromSaleService->createHandler($order, $orderCreateFromSaleForm, $saleData);
        $case->addEventLog(
            CaseEventLog::VOLUNTARY_REFUND_CREATE,
            'Origin ProductQuote created GID: ' . $originProductQuote->pq_gid,
            ['pq_gid' => $originProductQuote->pq_gid],
            CaseEventLog::CATEGORY_INFO
        );
        return $originProductQuote;
    }

    private static function getCaseCategoryKey(): string
    {
        if (!empty(SettingHelper::getVoluntaryRefundCaseCategory())) {
            return SettingHelper::getVoluntaryRefundCaseCategory();
        }
        return self::CASE_CREATE_CATEGORY_KEY;
    }

    private function errorHandler(
        ?Cases $case,
        ?ProductQuoteRefund $productQuoteRefund,
        string $description,
        ?\Throwable $exception
    ): void {
        if ($case) {
            $case->addEventLog(CaseEventLog::VOLUNTARY_REFUND_CREATE, $description, [], CaseEventLog::CATEGORY_ERROR);
            $case->offIsAutomate()->error(null, $description);
            $this->casesRepository->save($case);
        }

        if ($productQuoteRefund) {
            $productQuoteRefund->error();
            $this->productQuoteRefundRepository->save($productQuoteRefund);
        }

        if ($exception) {
            \Yii::error(AppHelper::throwableLog($exception, true), 'VoluntaryRefundService:errorHandler');
        }
    }
}
