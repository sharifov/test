<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\OrderRepository;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderPaymentPaidJob
 *
 * @property int $orderId
 */
class OrderPaymentPaidJob implements RetryableJobInterface
{
    public $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        $repo = \Yii::createObject(OrderRepository::class);
        $order = $repo->find($this->orderId);
        $order->paymentPaid(new \DateTimeImmutable());
        $repo->save($order);
    }

    public function getTtr(): int
    {
        return 5;
    }

    public function canRetry($attempt, $error): bool
    {
        \Yii::error([
            'attempt' => $attempt,
            'message' => 'Order payment Paid error',
            'error' => $error->getMessage(),
            'orderId' => $this->orderId,
        ], 'OrderPaymentPaidJob');
        return !($attempt > 5);
    }
}
