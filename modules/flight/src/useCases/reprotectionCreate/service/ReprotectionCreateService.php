<?php

namespace modules\flight\src\useCases\reprotectionCreate\service;

use common\models\CaseSale;
use common\models\Client;
use DomainException;
use modules\flight\models\Flight;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuoteFlight;
use modules\flight\models\FlightRequest;
use modules\flight\models\FlightRequestLog;
use modules\flight\src\repositories\flightRequest\FlightRequestRepository;
use modules\flight\src\repositories\flightRequestLog\FlightRequestLogRepository;
use modules\flight\src\useCases\flightQuote\FlightQuoteManageService;
use modules\flight\src\useCases\sale\FlightFromSaleService;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\orderContact\OrderContact;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleService;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuoteChange\ProductQuoteChange;
use modules\product\src\entities\productQuoteChange\ProductQuoteChangeRepository;
use sales\entities\cases\CaseEventLog;
use sales\entities\cases\Cases;
use sales\exception\ValidationException;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\helpers\app\AppHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\helpers\setting\SettingHelper;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesSaleService;
use sales\services\client\ClientCreateForm;
use sales\services\client\ClientManageService;
use sales\services\TransactionManager;
use Throwable;
use Yii;
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
 * @property FlightQuoteManageService $flightQuoteManageService
 * @property ProductQuoteChangeRepository $productQuoteChangeRepository
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
    private FlightQuoteManageService $flightQuoteManageService;
    private ProductQuoteChangeRepository $productQuoteChangeRepository;

    /**
     * @param CasesSaleService $casesSaleService
     * @param ClientManageService $clientManageService
     * @param CasesRepository $casesRepository
     * @param TransactionManager $transactionManager
     * @param OrderCreateFromSaleService $orderCreateFromSaleService
     * @param OrderRepository $orderRepository
     * @param FlightFromSaleService $flightFromSaleService
     * @param ProductQuoteRepository $productQuoteRepository
     * @param FlightQuoteManageService $flightQuoteManageService
     * @param ProductQuoteChangeRepository $productQuoteChangeRepository
     */
    public function __construct(
        CasesSaleService $casesSaleService,
        ClientManageService $clientManageService,
        CasesRepository $casesRepository,
        TransactionManager $transactionManager,
        OrderCreateFromSaleService $orderCreateFromSaleService,
        OrderRepository $orderRepository,
        FlightFromSaleService $flightFromSaleService,
        ProductQuoteRepository $productQuoteRepository,
        FlightQuoteManageService $flightQuoteManageService,
        ProductQuoteChangeRepository $productQuoteChangeRepository
    ) {
        $this->casesSaleService = $casesSaleService;
        $this->clientManageService = $clientManageService;
        $this->casesRepository = $casesRepository;
        $this->transactionManager = $transactionManager;
        $this->orderCreateFromSaleService = $orderCreateFromSaleService;
        $this->orderRepository = $orderRepository;
        $this->flightFromSaleService = $flightFromSaleService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->flightQuoteManageService = $flightQuoteManageService;
        $this->productQuoteChangeRepository = $productQuoteChangeRepository;
    }

    public function getOrCreateClient(int $projectId, OrderContactForm $orderContactForm): Client
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

    public function createOrder(
        OrderCreateFromSaleForm $orderCreateFromSaleForm,
        OrderContactForm $orderContactForm,
        Cases $case,
        int $projectId
    ): Order {
        $order = $this->orderCreateFromSaleService->orderCreate($orderCreateFromSaleForm);
        if (!$order->validate()) {
            throw new ValidationException(ErrorsToStringHelper::extractFromModel($order));
        }
        $order->or_project_id = $projectId;
        $orderId = $this->orderRepository->save($order);

        $this->orderCreateFromSaleService->caseOrderRelation($orderId, $case->cs_id);
        $this->orderCreateFromSaleService->orderContactCreate($order, $orderContactForm);

        $case->addEventLog(
            CaseEventLog::RE_PROTECTION_CREATE,
            'Order created GID: ' . $order->or_gid,
            ['order_gid' => $order->or_gid]
        );

        return $order;
    }

    public function createOriginProductQuoteInfrastructure(
        OrderCreateFromSaleForm $orderCreateFromSaleForm,
        array $saleData,
        Order $order,
        Cases $case,
        bool $productQuoteChangeIsAutomate
    ): ProductQuote {
        $originProductQuote = $this->flightFromSaleService->createHandler($order, $orderCreateFromSaleForm, $saleData);
        $case->addEventLog(
            CaseEventLog::RE_PROTECTION_CREATE,
            'Origin ProductQuote created GID: ' . $originProductQuote->pq_gid,
            ['pq_gid' => $originProductQuote->pq_gid]
        );
        $productQuoteChange = ProductQuoteChange::createNew($originProductQuote->pq_id, $case->cs_id, $productQuoteChangeIsAutomate);
        $this->productQuoteChangeRepository->save($productQuoteChange);
        return $originProductQuote;
    }

    public function createCaseSale(array $saleData, Cases $case): ?CaseSale
    {
        $caseSale = $this->casesSaleService->createSaleByData($case->cs_id, $saleData);
        $case->addEventLog(
            CaseEventLog::RE_PROTECTION_CREATE,
            'Case Sale created by Data',
            ['case_id' => $case->cs_id]
        );
        return $caseSale;
    }

    public function originProductQuoteDecline(ProductQuote $originProductQuote, Cases $case): ProductQuote
    {
        if (!$originProductQuote->isDeclined()) {
            $originProductQuote->declined();
            $this->productQuoteRepository->save($originProductQuote);
            $case->addEventLog(
                CaseEventLog::RE_PROTECTION_CREATE,
                'Origin ProductQuote declined',
                ['pq_gid' => $originProductQuote->pq_gid]
            );
        }
        return $originProductQuote;
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
            return ArrayHelper::getValue($flightQuoteFlight, 'fqfFq.fqProductQuote.pqOrder');
        }
        return null;
    }

    public function getFlightByBookingId(string $bookingId): ?Flight
    {
        if ($flightQuoteFlight = FlightQuoteFlight::find()->where(['fqf_booking_id' => $bookingId])->orderBy(['fqf_id' => SORT_DESC])->one()) {
            return ArrayHelper::getValue($flightQuoteFlight, 'fqfFq.fqFlight');
        }
        return null;
    }

    public function getFlightByOriginQuote(ProductQuote $originProductQuote): Flight
    {
        if ($flight = $originProductQuote->flightQuote->fqFlight ?? null) {
            return $flight;
        }
        throw new DomainException('Flight by OriginQuote not found');
    }

    public function declineReProtectionQuotes(int $originProductQuoteId, string $originProductQuoteGid, Cases $case, ?int $userId = null): array
    {
        $declinedIds = [];
        if ($reProtectionQuotes = ProductQuoteQuery::getReprotectionQuotesByOriginQuote($originProductQuoteId)) {
            foreach ($reProtectionQuotes as $reProtectionQuote) {
                if (!$reProtectionQuote->isDeclined() && self::isReProtectionQuote($reProtectionQuote)) {
                    $reProtectionQuote->declined($userId, 'Declined from reProtection');
                    $this->productQuoteRepository->save($reProtectionQuote);
                    $declinedIds[] = $reProtectionQuote->pq_id;
                }
            }
            if ($declinedIds) {
                $case->addEventLog(
                    CaseEventLog::RE_PROTECTION_CREATE,
                    'Old ReProtectionQuotes declined',
                    ['originProductQuoteGid' => $originProductQuoteGid]
                );
            }
        }
        return $declinedIds;
    }

    private static function isReProtectionQuote(ProductQuote $reProtectionQuote): bool
    {
        return $reProtectionQuote->isFlight() && $reProtectionQuote->flightQuote->isTypeReProtection();
    }

    public function flightRequestChangeStatus(FlightRequest $flightRequest, int $newStatus, $description): FlightRequest
    {
        $oldStatus = $flightRequest->fr_status_id;
        if ($newStatus === FlightRequest::STATUS_PENDING) {
            $flightRequest->statusToPending();
        } elseif ($newStatus === FlightRequest::STATUS_ERROR) {
            $flightRequest->statusToError();
        } elseif ($newStatus === FlightRequest::STATUS_DONE) {
            $flightRequest->statusToDone();
        } else {
            $flightRequest->fr_status_id = $newStatus;
        }
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

    public function getClientByOrderProject(int $orderId, int $projectId): ?Client
    {
        return Client::find()
            ->select(Client::tableName() . '.*')
            ->innerJoin(OrderContact::tableName() . ' AS order_contact', Client::tableName() . '.id = order_contact.oc_client_id')
            ->where(['cl_project_id' => $projectId])
            ->andWhere(['oc_order_id' => $orderId])
            ->orderBy([Client::tableName() . '.id' => SORT_DESC])
            ->one();
    }

    public function createSimpleClient(int $projectId): Client
    {
        $clientForm = ClientCreateForm::createWidthDefaultName();
        $clientForm->projectId = $projectId;
        $clientForm->typeCreate = Client::TYPE_CREATE_CASE;
        return $this->clientManageService->create($clientForm, null);
    }

    public static function writeLog(Throwable $throwable, array $data = [], string $category = 'ReprotectionCreateJob:throwable'): void
    {
        $message = AppHelper::throwableLog($throwable);
        if ($data) {
            $message = ArrayHelper::merge($message, $data);
        }
        if ($throwable instanceof DomainException) {
            Yii::warning($message, $category);
        } else {
            Yii::error($message, $category);
        }
    }
}
