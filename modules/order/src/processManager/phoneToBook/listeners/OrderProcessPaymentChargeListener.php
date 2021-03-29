<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use common\models\Payment;
use modules\order\src\entities\order\events\OrderPreparedEvent;
use modules\order\src\payment\jobs\ChargePaymentJob;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\queue\Queue;

/**
 * Class OrderProcessPaymentChargeListener
 *
 * @property OrderProcessManagerRepository $repository
 * @property Queue $queue
 */
class OrderProcessPaymentChargeListener
{
    private OrderProcessManagerRepository $repository;
    private Queue $queue;

    public function __construct(OrderProcessManagerRepository $repository, Queue $queue)
    {
        $this->repository = $repository;
        $this->queue = $queue;
    }

    public function handle(OrderPreparedEvent $event): void
    {
        $manager = $this->repository->get($event->orderId);

        if (!$manager) {
            return;
        }

        if (!$manager->isBooked()) {
            return;
        }

        $payment = Payment::find()->andWhere(['pay_order_id' => $event->orderId])->one();

        if (!$payment) {
            \Yii::error([
                'message' => 'Payment charge error',
                'error' => 'Not found payment',
                'orderId' => $event->orderId,
            ], 'OrderProcessPaymentChargeListener');
            return;
        }

        $this->queue->push(new ChargePaymentJob($payment->pay_id));
    }
}
