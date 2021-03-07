<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderStatus;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderCompletedHybridNotificationJob
 *
 * @property $orderId
 */
class OrderCompletedHybridNotificationJob implements JobInterface
{
    public $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        $order = Order::findOne($this->orderId);

        if (!$order) {
            \Yii::error([
                'message' => 'Not found Order',
                'orderId' => $this->orderId,
            ], 'OrderCompletedHybridNotificationJob');
        }

        \Yii::info([
            'message' => 'Send completed status to hybrid',
            'orderId' => $this->orderId,
            'status' => $order->or_status_id,
            'statusName' => OrderStatus::getName($order->or_status_id),
        ], 'info\OrderCompletedHybridNotificationJob');
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
//            'message' => 'Order completed hybrid notification error',
//            'error' => $error->getMessage(),
//            'orderId' => $this->orderId,
//        ], 'OrderCompletedHybridNotificationJob');
//        return !($attempt > 5);
//    }
}
