<?php

namespace modules\order\src\services\createFromSale;

use common\components\SearchService;
use common\models\Payment;
use modules\flight\models\Flight;
use modules\flight\models\FlightPax;
use modules\flight\models\FlightQuote;
use modules\flight\models\FlightQuotePaxPrice;
use modules\flight\models\FlightQuoteSegment;
use modules\flight\models\FlightQuoteStatusLog;
use modules\flight\models\FlightQuoteTicket;
use modules\flight\models\FlightQuoteTrip;
use modules\flight\src\repositories\flightQuoteRepository\FlightQuoteRepository;
use modules\flight\src\repositories\flightQuoteSegment\FlightQuoteSegmentRepository;
use modules\flight\src\repositories\flightQuoteTripRepository\FlightQuoteTripRepository;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteCreateDTO;
use modules\flight\src\useCases\flightQuote\create\FlightQuoteSegmentDTO;
use modules\flight\src\useCases\flightQuote\create\ProductQuoteCreateDTO;
use modules\flight\src\useCases\sale\form\OrderContactForm;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\entities\orderContact\OrderContact;
use modules\order\src\payment\helpers\PaymentHelper;
use modules\order\src\payment\method\PaymentMethodRepository;
use modules\order\src\payment\PaymentRepository;
use modules\order\src\services\CreateOrderDTO;
use modules\order\src\services\OrderContactManageService;
use modules\product\src\entities\product\Product;
use modules\product\src\entities\product\ProductRepository;
use modules\product\src\entities\productHolder\ProductHolder;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productType\ProductTypeRepository;
use modules\product\src\useCases\product\create\ProductCreateForm;
use modules\product\src\useCases\product\create\ProductCreateService;
use sales\helpers\ErrorsToStringHelper;
use sales\model\caseOrder\entity\CaseOrder;
use sales\repositories\product\ProductQuoteRepository;
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

    /**
     * @param PaymentRepository $paymentRepository
     * @param ProductCreateService $productCreateService
     * @param ProductQuoteRepository $productQuoteRepository
     * @param FlightQuoteRepository $flightQuoteRepository
     * @param FlightQuoteTripRepository $flightQuoteTripRepository
     * @param FlightQuoteSegmentRepository $flightQuoteSegmentRepository
     * @param ProductRepository $productRepository
     * @param OrderContactManageService $orderContactManageService
     */
    public function __construct(
        PaymentRepository $paymentRepository,
        ProductCreateService $productCreateService,
        ProductQuoteRepository $productQuoteRepository,
        FlightQuoteRepository $flightQuoteRepository,
        FlightQuoteTripRepository $flightQuoteTripRepository,
        FlightQuoteSegmentRepository $flightQuoteSegmentRepository,
        ProductRepository $productRepository,
        OrderContactManageService $orderContactManageService
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->productCreateService = $productCreateService;
        $this->productQuoteRepository = $productQuoteRepository;
        $this->flightQuoteRepository = $flightQuoteRepository;
        $this->flightQuoteTripRepository = $flightQuoteTripRepository;
        $this->flightQuoteSegmentRepository = $flightQuoteSegmentRepository;
        $this->productRepository = $productRepository;
        $this->orderContactManageService = $orderContactManageService;
    }

    public function orderCreate(
        OrderCreateFromSaleForm $form,
        int $saleId,
        ?float $appTotal,
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
            $saleId
        );
        $order = (new Order())->create($dto);
        $order->or_pay_status_id = $payStatusId;
        $order->or_app_total = $appTotal;
        $order->or_client_total = $appTotal;
        return $order;
    }

    public function orderContactCreate(Order $order, OrderContactForm $orderContactForm): OrderContact
    {
        return $this->orderContactManageService->create(
            $order->or_id,
            $orderContactForm->first_name,
            $orderContactForm->last_name,
            null,
            $orderContactForm->email,
            $orderContactForm->phone_number,
            $order->or_project_id,
            5
        );
    }

    public function caseOrderRelation(int $orderId, int $caseId): bool
    {
        if (!CaseOrder::findOne(['co_order_id' => $orderId, 'co_case_id' => $caseId])) {
            $caseOrder = CaseOrder::create($caseId, $orderId);
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
