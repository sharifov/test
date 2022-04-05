<?php

namespace modules\order\src\services\createFromSale;

use common\models\Currency;
use common\models\Payment;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\repositories\flightQuoteSegment\FlightQuoteSegmentRepository;
use modules\flight\src\repositories\flightQuoteTripRepository\FlightQuoteTripRepository;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\entities\orderContact\OrderContact;
use modules\order\src\entities\orderContact\OrderContactRepository;
use modules\order\src\payment\helpers\PaymentHelper;
use modules\order\src\payment\PaymentRepository;
use modules\order\src\services\CreateOrderDTO;
use modules\order\src\services\OrderContactManageService;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\useCases\product\create\ProductCreateService;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\model\caseOrder\entity\CaseOrder;
use src\repositories\product\ProductQuoteRepository;
use src\services\client\ClientManageService;
use src\services\CurrencyHelper;
use yii\helpers\ArrayHelper;

/**
 * Class OrderCreateFromSaleService
 *
 * @property PaymentRepository $paymentRepository
 * @property ProductCreateService $productCreateService
 * @property ProductQuoteRepository $productQuoteRepository
 * @property FlightQuoteRepository $flightQuoteRepository
 * @property FlightQuoteTripRepository $flightQuoteTripRepository
 * @property FlightQuoteSegmentRepository $flightQuoteSegmentRepository
 * @property ProductRepository $productRepository
 * @property OrderContactManageService $orderContactManageService
 * @property ClientManageService $clientManageService
 * @property OrderContactRepository $orderContactRepository
 */
class OrderCreateFromSaleService
{
    private PaymentRepository $paymentRepository;
    private ProductCreateService $productCreateService;
    private ProductQuoteRepository $productQuoteRepository;
    private FlightQuoteRepository $flightQuoteRepository;
    private FlightQuoteTripRepository $flightQuoteTripRepository;
    private FlightQuoteSegmentRepository $flightQuoteSegmentRepository;
    private ProductRepository $productRepository;
    private OrderContactManageService $orderContactManageService;
    private ClientManageService $clientManageService;
    private OrderContactRepository $orderContactRepository;

    /**
     * @param PaymentRepository $paymentRepository
     * @param ProductCreateService $productCreateService
     * @param ProductQuoteRepository $productQuoteRepository
     * @param FlightQuoteRepository $flightQuoteRepository
     * @param FlightQuoteTripRepository $flightQuoteTripRepository
     * @param FlightQuoteSegmentRepository $flightQuoteSegmentRepository
     * @param ProductRepository $productRepository
     * @param OrderContactManageService $orderContactManageService
     * @param ClientManageService $clientManageService
     * @param OrderContactRepository $orderContactRepository
     */
    public function __construct(
        PaymentRepository $paymentRepository,
        ProductCreateService $productCreateService,
        ProductQuoteRepository $productQuoteRepository,
        FlightQuoteRepository $flightQuoteRepository,
        FlightQuoteTripRepository $flightQuoteTripRepository,
        FlightQuoteSegmentRepository $flightQuoteSegmentRepository,
        ProductRepository $productRepository,
        OrderContactManageService $orderContactManageService,
        ClientManageService $clientManageService,
        OrderContactRepository $orderContactRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->productCreateService = $productCreateService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->flightQuoteRepository = $flightQuoteRepository;
        $this->flightQuoteTripRepository = $flightQuoteTripRepository;
        $this->flightQuoteSegmentRepository = $flightQuoteSegmentRepository;
        $this->productRepository = $productRepository;
        $this->orderContactManageService = $orderContactManageService;
        $this->clientManageService = $clientManageService;
        $this->orderContactRepository = $orderContactRepository;
    }

    /**
     * @param OrderCreateFromSaleForm $form
     * @param int $payStatusId
     * @return Order
     */
    public function orderCreate(
        OrderCreateFromSaleForm $form,
        $payStatusId = OrderPayStatus::PAID
    ): Order {
        $dto = new CreateOrderDTO(
            null,
            $form->currency,
            [],
            OrderSourceType::SALE,
            null,
            $form->getProjectId(),
            OrderStatus::COMPLETE,
            null,
            null,
            null,
            $form->saleId
        );
        $order = (new Order())->create($dto);
        $order->or_pay_status_id = $payStatusId;
        $order->or_client_currency_rate = $form->currency === Currency::DEFAULT_CURRENCY ?
            Currency::DEFAULT_CURRENCY_CLIENT_RATE :
            CurrencyHelper::getAppRateByCode($form->currency);
        return $order;
    }

    public function orderContactCreate(Order $order, OrderContactForm $orderContactForm): ?OrderContact
    {
        try {
            if (!$orderContactForm->validate()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($orderContactForm));
            }

            $orderContact = OrderContact::create(
                $order->or_id,
                $orderContactForm->first_name,
                $orderContactForm->last_name,
                null,
                $orderContactForm->email,
                $orderContactForm->phone_number
            );

            $client = $this->clientManageService->createBasedOnOrderContact($orderContact, $order->or_project_id);
            $orderContact->oc_client_id = $client->id;
            $this->orderContactRepository->save($orderContact);

            return $orderContact;
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = AppHelper::throwableLog($throwable, true);
            $message['orderContactForm'] = $orderContactForm->toArray();
            \Yii::warning(
                $message,
                'OrderCreateFromSaleService:orderContactCreate:Exception'
            );
        } catch (\Throwable $throwable) {
            \Yii::error(
                AppHelper::throwableLog($throwable, true),
                'OrderCreateFromSaleService:orderContactCreate:Throwable'
            );
        }
        return null;
    }

    public function caseOrderRelation(int $orderId, int $caseId): bool
    {
        if (!CaseOrder::findOne(['co_order_id' => $orderId, 'co_case_id' => $caseId])) {
            $caseOrder = CaseOrder::create($caseId, $orderId);
            $caseOrder->detachBehavior('user');
            if (!$caseOrder->validate()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($caseOrder));
            }
            $caseOrder->save();
            return true;
        }
        return false;
    }

    public function paymentCreate(array $authList, int $orderId, ?string $currency): array
    {
        $result = [];
        foreach ($authList as $value) {
            if ($payDate = ArrayHelper::getValue($value, 'created')) {
                $payDate = date('Y-m-d', strtotime($payDate));
            }
            $payment = Payment::create(
                null,
                $payDate,
                ArrayHelper::getValue($value, 'amount'),
                $currency,
                null,
                $orderId,
                null,
                ArrayHelper::getValue($value, 'message'),
                null
            );
            $payment->setStatus(PaymentHelper::detectStatusFromSale(ArrayHelper::getValue($value, 'status')));
            if (!$payment->validate()) {
                $paymentWarning = $payment->getErrors();
                $paymentWarning['data'] = $value;
                \Yii::warning($paymentWarning, 'OrderCreateFromSaleService:PaymentCreate');
            } else {
                $this->paymentRepository->save($payment);
                $result[] = $payment;
            }
        }
        return $result;
    }
}
