<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\Order;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderProcessingConfirmationJob
 *
 * @property $orderId
 */
class OrderProcessingConfirmationJob implements RetryableJobInterface
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
            ], 'OrderProcessingConfirmationJob');
            return;
        }

        \Yii::info([
            'message' => 'Send processing confirmation email',
            'orderId' => $this->orderId,
            'data' => $order->serialize(),
        ], 'info\ProcessingConfirmationJob');
    }

    public function getTtr(): int
    {
        return 1 * 60;
    }

    public function canRetry($attempt, $error): bool
    {
        \Yii::error([
            'attempt' => $attempt,
            'message' => 'Order processing confirmation error',
            'error' => $error->getMessage(),
            'orderId' => $this->orderId,
        ], 'OrderProcessingConfirmationJob');
        return !($attempt > 5);
    }
}
