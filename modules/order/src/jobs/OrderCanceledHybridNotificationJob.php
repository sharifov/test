<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderStatus;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderCanceledHybridNotificationJob
 *
 * @property $orderId
 */
class OrderCanceledHybridNotificationJob implements JobInterface
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
            ], 'OrderCanceledHybridNotificationJob');
        }
        try {
            \Yii::$app->hybrid->updateStatus($order->orLead->project_id, $order->or_gid, OrderStatus::CANCELED);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Send canceled status to hybrid',
                'orderId' => $this->orderId,
                'status' => OrderStatus::CANCELED,
                'error' => $e->getMessage(),
            ], 'OrderCanceledHybridNotificationJob');
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
//            'message' => 'Order canceled hybrid notification error',
//            'error' => $error->getMessage(),
//            'orderId' => $this->orderId,
//        ], 'OrderCanceledHybridNotificationJob');
//        return !($attempt > 5);
//    }
}
