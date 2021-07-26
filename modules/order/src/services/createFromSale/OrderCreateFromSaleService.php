<?php

namespace modules\order\src\services\createFromSale;

use common\models\Payment;
use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\entities\order\OrderStatus;
use modules\order\src\payment\helpers\PaymentHelper;
use modules\order\src\payment\method\PaymentMethodRepository;
use modules\order\src\payment\PaymentRepository;
use modules\order\src\services\CreateOrderDTO;
use yii\helpers\ArrayHelper;

/**
 * Class OrderCreateFromSaleService
 *
 * @property PaymentRepository $paymentRepository
 */
class OrderCreateFromSaleService
{
    private PaymentRepository $paymentRepository;

    /**
     * @param PaymentRepository $paymentRepository
     */
    public function __construct(
        PaymentRepository $paymentRepository
    ) {
        $this->paymentRepository = $paymentRepository;
    }

    public function orderCreate(OrderCreateFromSaleForm $form, int $saleId): Order
    {
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
        return (new Order())->create($dto);
    }

    public function paymentCreate(array $authList, int $orderId): array
    {
        $result = [];
        foreach ($authList as $value) {
            $payment = Payment::create(
                null,
                ArrayHelper::getValue($value, 'created'),
                ArrayHelper::getValue($value, 'amount'),
                null,
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
