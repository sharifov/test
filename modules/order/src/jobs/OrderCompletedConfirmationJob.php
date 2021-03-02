<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\Order;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderCompletedConfirmationJob
 *
 * @property $orderId
 */
class OrderCompletedConfirmationJob implements RetryableJobInterface
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
            ], 'OrderCompletedConfirmationJob');
        }

        \Yii::info([
            'message' => 'Send completed confirmation email',
            'orderId' => $this->orderId,
            'data' => $order->serialize(),
        ], 'info\OrderCompletedConfirmationJob');
    }

    public function getTtr(): int
    {
        return 1 * 60;
    }

    public function canRetry($attempt, $error): bool
    {
        \Yii::error([
            'attempt' => $attempt,
            'message' => 'Order completed confirmation error',
            'error' => $error->getMessage(),
            'orderId' => $this->orderId,
        ], 'OrderCompletedConfirmationJob');
        return !($attempt > 5);
    }
}
