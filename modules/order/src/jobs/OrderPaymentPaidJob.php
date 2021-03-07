<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\OrderRepository;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderPaymentPaidJob
 *
 * @property int $orderId
 */
class OrderPaymentPaidJob implements JobInterface
{
    public $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        try {
            $repo = \Yii::createObject(OrderRepository::class);
            $order = $repo->find($this->orderId);
            $order->paymentPaid(new \DateTimeImmutable());
            $repo->save($order);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Cant transfer order to payment paid',
                'error' => $e->getMessage(),
                'orderId' => $this->orderId,
            ], 'OrderPaymentPaidJob');
        }
    }

//    public function getTtr(): int
//    {
//        return 1 * 60;
//    }
//
//    public function canRetry($attempt, $error): bool
//    {
//        \Yii::error([
//            'attempt' => $attempt,
//            'message' => 'Order payment Paid error',
//            'error' => $error->getMessage(),
//            'orderId' => $this->orderId,
//        ], 'OrderPaymentPaidJob');
//        return !($attempt > 5);
//    }
}
