<?php

namespace modules\flight\src\useCases\reprotectionCreate\service;

use common\models\Client;
use modules\flight\src\useCases\sale\FlightFromSaleService;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderRepository;
use modules\order\src\services\createFromSale\OrderCreateFromSaleForm;
use modules\order\src\services\createFromSale\OrderCreateFromSaleService;
use sales\entities\cases\CaseCategory;
use sales\entities\cases\Cases;
use sales\forms\lead\EmailCreateForm;
use sales\forms\lead\PhoneCreateForm;
use sales\repositories\cases\CasesRepository;
use sales\services\cases\CasesSaleService;
use sales\services\client\ClientCreateForm;
use sales\services\client\ClientManageService;
use sales\services\TransactionManager;
use yii\helpers\ArrayHelper;

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

    /**
     * @param CasesSaleService $casesSaleService
     * @param ClientManageService $clientManageService
     * @param CasesRepository $casesRepository
     * @param TransactionManager $transactionManager
     * @param OrderCreateFromSaleService $orderCreateFromSaleService
     * @param OrderRepository $orderRepository
     * @param FlightFromSaleService $flightFromSaleService
     */
    public function __construct(
        CasesSaleService $casesSaleService,
        ClientManageService $clientManageService,
        CasesRepository $casesRepository,
        TransactionManager $transactionManager,
        OrderCreateFromSaleService $orderCreateFromSaleService,
        OrderRepository $orderRepository,
        FlightFromSaleService $flightFromSaleService
    ) {
        $this->casesSaleService = $casesSaleService;
        $this->clientManageService = $clientManageService;
        $this->casesRepository = $casesRepository;
        $this->transactionManager = $transactionManager;
        $this->orderCreateFromSaleService = $orderCreateFromSaleService;
        $this->orderRepository = $orderRepository;
        $this->flightFromSaleService = $flightFromSaleService;
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

    public function createCase(OrderCreateFromSaleForm $orderCreateFromSaleForm, Client $client)
    {
        if (!$caseCategory = CaseCategory::findOne(['cc_key' => self::CASE_CATEGORY_SCHEDULE_CHANGE])) {
            throw new \RuntimeException('CaseCategory (' . self::CASE_CATEGORY_SCHEDULE_CHANGE . ') is required');
        }

        $case = Cases::createByApi(
            $client->id,
            $orderCreateFromSaleForm->projectId,
            $caseCategory->cc_dep_id,
            null,
            null,
            'Flight Schedule Change',
            $caseCategory->cc_id
        );
        $this->casesRepository->save($case);

        return $case;
    }

    public function createOrder(OrderCreateFromSaleForm $orderCreateFromSaleForm, OrderContactForm $orderContactForm, Cases $case): Order
    {
        $order = $this->orderCreateFromSaleService->orderCreate($orderCreateFromSaleForm);
        $orderId = $this->orderRepository->save($order);

        $this->orderCreateFromSaleService->caseOrderRelation($orderId, $case->cs_id);
        $this->orderCreateFromSaleService->orderContactCreate($order, $orderContactForm);

        return $order;
    }

    public function createFlight(OrderCreateFromSaleForm $orderCreateFromSaleForm, array $saleData, Order $order)
    {
        $this->flightFromSaleService->createHandler($order, $orderCreateFromSaleForm, $saleData);
    }

    public function createPayment(OrderCreateFromSaleForm $orderCreateFromSaleForm, array $saleData, Order $order): ?array
    {
        if ($authList = ArrayHelper::getValue($saleData, 'authList')) {
            return $this->orderCreateFromSaleService->paymentCreate($authList, $order->getId(), $orderCreateFromSaleForm->currency);
        }
        return null;
    }
}
