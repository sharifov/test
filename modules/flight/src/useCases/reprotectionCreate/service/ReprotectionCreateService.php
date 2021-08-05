<?php

namespace modules\flight\src\useCases\reprotectionCreate\service;

use common\models\Client;
use common\models\Project;
use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\models\FlightRequest;
use modules\flight\models\FlightRequestLog;
use modules\flight\src\repositories\flightRequest\FlightRequestRepository;
use modules\flight\src\repositories\flightRequestLog\FlightRequestLogRepository;
use modules\flight\src\useCases\sale\FlightFromSaleService;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleService;
use modules\product\src\entities\productQuote\ProductQuote;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\Cases;
use sales\exception\CheckRestrictionException;
use sales\exception\ValidationException;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\helpers\ErrorsToStringHelper;
use sales\helpers\setting\SettingHelper;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesSaleService;
use sales\services\client\ClientCreateForm;
use sales\services\client\ClientManageService;
use sales\services\TransactionManager;
use yii\helpers\ArrayHelper;
use sales\repositories\product\ProductQuoteRepository;
use yii\helpers\VarDumper;

/**
 * Class ReprotectionCreateService
 *
 * @property CasesSaleService $casesSaleService
 * @property ClientManageService $clientManageService
 * @property CasesRepository $casesRepository
 * @property TransactionManager $transactionManager
 * @property OrderCreateFromSaleService $orderCreateFromSaleService
 * @property OrderRepository $orderRepository
 * @property FlightFromSaleService $flightFromSaleService
 * @property ProductQuoteRepository $productQuoteRepository
 */
class ReprotectionCreateService
{
    public const CASE_CATEGORY_SCHEDULE_CHANGE = 'flight_schedule_change';
    public const CASE_CATEGORY_SCHEDULE_REFUND = 'flight_schedule_refund';

    public const CASE_CATEGORY_LIST = [
        self::CASE_CATEGORY_SCHEDULE_CHANGE => 'Flight Schedule Change',
        self::CASE_CATEGORY_SCHEDULE_REFUND => 'Flight Schedule Refund',
    ];

    private CasesSaleService $casesSaleService;
    private ClientManageService $clientManageService;
    private CasesRepository $casesRepository;
    private TransactionManager $transactionManager;
    private OrderCreateFromSaleService $orderCreateFromSaleService;
    private OrderRepository $orderRepository;
    private FlightFromSaleService $flightFromSaleService;
    private ProductQuoteRepository $productQuoteRepository;

    /**
     * @param CasesSaleService $casesSaleService
     * @param ClientManageService $clientManageService
     * @param CasesRepository $casesRepository
     * @param TransactionManager $transactionManager
     * @param OrderCreateFromSaleService $orderCreateFromSaleService
     * @param OrderRepository $orderRepository
     * @param FlightFromSaleService $flightFromSaleService
     * @param ProductQuoteRepository $productQuoteRepository
     */
    public function __construct(
        CasesSaleService $casesSaleService,
        ClientManageService $clientManageService,
        CasesRepository $casesRepository,
        TransactionManager $transactionManager,
        OrderCreateFromSaleService $orderCreateFromSaleService,
        OrderRepository $orderRepository,
        FlightFromSaleService $flightFromSaleService,
        ProductQuoteRepository $productQuoteRepository
    ) {
        $this->casesSaleService = $casesSaleService;
        $this->clientManageService = $clientManageService;
        $this->casesRepository = $casesRepository;
        $this->transactionManager = $transactionManager;
        $this->orderCreateFromSaleService = $orderCreateFromSaleService;
        $this->orderRepository = $orderRepository;
        $this->flightFromSaleService = $flightFromSaleService;
        $this->productQuoteRepository = $productQuoteRepository;
    }

    public function getOrCreateClient(OrderCreateFromSaleForm $orderCreateFromSaleForm, OrderContactForm $orderContactForm): Client
    {
        $clientForm = new ClientCreateForm();
        $clientForm->projectId = $orderCreateFromSaleForm->projectId;
        $clientForm->typeCreate = Client::TYPE_CREATE_CASE;
        $clientForm->firstName = $orderContactForm->first_name;
        $clientForm->lastName = $orderContactForm->last_name;

        return $this->clientManageService->getOrCreate(
            [new PhoneCreateForm(['phone' => $orderContactForm->phone_number])],
            [new EmailCreateForm(['email' => $orderContactForm->email])],
            $clientForm
        );
    }

    public function createCase(string $bookingId): Cases
    {
        if (!$caseCategoryKey = SettingHelper::getReProtectionCaseCategory()) {
            throw new CheckRestrictionException('Setting "reprotection_case_category" is empty');
        }
        if (!$caseCategory = CaseCategory::findOne(['cc_key' => $caseCategoryKey])) {
            throw new CheckRestrictionException('CaseCategory (' . $caseCategoryKey . ') not found');
        }

        $case = Cases::createByApiReProtection(
            $caseCategory->cc_dep_id,
            $caseCategory->cc_id,
            $bookingId
        );
        $this->casesRepository->save($case);
        return $case;
    }

    public function additionalFillingCase(Cases $case, ?int $clientId, ?int $projectId): Cases
    {
        $case->cs_client_id = $clientId;
        $case->cs_project_id = $projectId;
        $this->casesRepository->save($case);
        return $case;
    }

    public function caseToManual(Cases $case, string $description): Cases
    {
        $case->offIsAutomate();
        if (!$case->isPending()) {
            $case->pending(null, $description);
        }
        $this->casesRepository->save($case);
        return $case;
    }

    public function createCaseOld(OrderCreateFromSaleForm $orderCreateFromSaleForm, Client $client)
    {
        if (!$caseCategory = CaseCategory::findOne(['cc_key' => self::CASE_CATEGORY_SCHEDULE_CHANGE])) {
            throw new \RuntimeException('CaseCategory (' . self::CASE_CATEGORY_SCHEDULE_CHANGE . ') is required');
        }

        $case = Cases::createByApi(
            $client->id,
            $orderCreateFromSaleForm->projectId,
            $caseCategory->cc_dep_id,
            $orderCreateFromSaleForm->bookingId,
            'Flight Schedule Change',
            null,
            $caseCategory->cc_id
        );
        $this->casesRepository->save($case);

        return $case;
    }

    public function createOrder(OrderCreateFromSaleForm $orderCreateFromSaleForm, OrderContactForm $orderContactForm, Cases $case): Order
    {
        $order = $this->orderCreateFromSaleService->orderCreate($orderCreateFromSaleForm);
        if (!$order->validate()) {
            throw new ValidationException(ErrorsToStringHelper::extractFromModel($order));
        }
        $orderId = $this->orderRepository->save($order);

        $this->orderCreateFromSaleService->caseOrderRelation($orderId, $case->cs_id);
        $this->orderCreateFromSaleService->orderContactCreate($order, $orderContactForm);

        return $order;
    }

    public function createFlightInfrastructure(OrderCreateFromSaleForm $orderCreateFromSaleForm, array $saleData, Order $order): ProductQuote
    {
        return $this->flightFromSaleService->createHandler($order, $orderCreateFromSaleForm, $saleData);
    }

    public function createPayment(OrderCreateFromSaleForm $orderCreateFromSaleForm, array $saleData, Order $order): ?array
    {
        if ($authList = ArrayHelper::getValue($saleData, 'authList')) {
            return $this->orderCreateFromSaleService->paymentCreate($authList, $order->getId(), $orderCreateFromSaleForm->currency);
        }
        return null;
    }

    public function getOrderByBookingId(string $bookingId): ?Order
    {
        if ($flightQuoteFlight = FlightQuoteFlight::find()->where(['fqf_booking_id' => $bookingId])->orderBy(['fqf_id' => SORT_DESC])->one()) {
            return $flightQuoteFlight->fqfFq->fqProductQuote->pqOrder ?? null;
        }
        return null;
    }

    public function getFlightByBookingId(string $bookingId): ?Flight
    {
        if ($flightQuoteFlight = FlightQuoteFlight::find()->where(['fqf_booking_id' => $bookingId])->orderBy(['fqf_id' => SORT_DESC])->one()) {
            return $flightQuoteFlight->fqfFq->fqFlight ?? null;
        }
        return null;
    }

    public function declineOldProductQuote(Order $order): ?ProductQuote
    {
        $oldProductQuote = null;
        if ($productQuotes = $order->productQuotes) {
            foreach ($productQuotes as $productQuote) {
                if ($productQuote->isFlight() && !$productQuote->isDeclined()) {
                    $productQuote->declined(null, 'Declined from reProtection');
                    $this->productQuoteRepository->save($productQuote);
                    $oldProductQuote = $productQuote;
                }
            }
        }
        return $oldProductQuote;
    }

    public function setCaseDeadline(Cases $case, FlightQuote $flightQuote): Cases
    {
        if (!(($firstSegment = $flightQuote->flightQuoteSegments[0]) && $firstSegment->fqs_departure_dt)) {
            throw new \RuntimeException('Deadline not created. Reason - Segments departure not correct');
        }
        $schdCaseDeadlineHours = SettingHelper::getSchdCaseDeadlineHours();
        $deadline = date('Y-m-d H:i:s', strtotime($firstSegment->fqs_departure_dt . ' -' . $schdCaseDeadlineHours . ' hours'));

        if ($deadline === false) {
            throw new \RuntimeException('Deadline not created');
        }
        $case->cs_deadline_dt = $deadline;
        $this->casesRepository->save($case);
        return $case;
    }

    public function flightRequestToPending(FlightRequest $flightRequest, $description): FlightRequest
    {
        $oldStatus = $flightRequest->fr_status_id;
        $flightRequest->statusToPending();
        (new FlightRequestRepository())->save($flightRequest);

        $flightRequestLog = FlightRequestLog::create(
            $flightRequest->fr_id,
            $oldStatus,
            $flightRequest->fr_status_id,
            VarDumper::dumpAsString($description)
        );
        (new FlightRequestLogRepository())->save($flightRequestLog);

        return $flightRequest;
    }

    public function flightRequestToError(FlightRequest $flightRequest, $description): FlightRequest
    {
        $oldStatus = $flightRequest->fr_status_id;
        $flightRequest->statusToError();
        (new FlightRequestRepository())->save($flightRequest);

        $flightRequestLog = FlightRequestLog::create(
            $flightRequest->fr_id,
            $oldStatus,
            $flightRequest->fr_status_id,
            VarDumper::dumpAsString($description)
        );
        (new FlightRequestLogRepository())->save($flightRequestLog);

        return $flightRequest;
    }
}
