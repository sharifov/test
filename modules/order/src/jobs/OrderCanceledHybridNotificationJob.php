<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\Order;
use modules\order\src\entities\order\OrderStatus;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderCanceledHybridNotificationJob
 *
 * @property $orderId
 */
class OrderCanceledHybridNotificationJob implements RetryableJobInterface
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

        \Yii::info([
            'message' => 'Send canceled status to hybrid',
            'orderId' => $this->orderId,
            'status' => $order->or_status_id,
            'statusName' => OrderStatus::getName($order->or_status_id),
        ], 'info\OrderCanceledHybridNotificationJob');
    }

    public function getTtr(): int
    {
        return 1 * 60;
    }

    public function canRetry($attempt, $error): bool
    {
        \Yii::error([
            'attempt' => $attempt,
            'message' => 'Order canceled hybrid notification error',
            'error' => $error->getMessage(),
            'orderId' => $this->orderId,
        ], 'OrderCanceledHybridNotificationJob');
        return !($attempt > 5);
    }
}
