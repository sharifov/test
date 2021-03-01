<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\Order;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderCanceledConfirmationJob
 *
 * @property $orderId
 */
class OrderCanceledConfirmationJob implements RetryableJobInterface
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
            ], 'OrderCanceledConfirmationJob');
        }

        \Yii::info([
            'message' => 'Send canceled confirmation email',
            'orderId' => $this->orderId,
            'data' => $order->serialize(),
        ], 'info\OrderCanceledConfirmationJob');
    }

    public function getTtr(): int
    {
        return 5;
    }

    public function canRetry($attempt, $error): bool
    {
        \Yii::error([
            'attempt' => $attempt,
            'message' => 'Order canceled confirmation error',
            'error' => $error->getMessage(),
            'orderId' => $this->orderId,
        ], 'OrderCanceledConfirmationJob');
        return !($attempt > 5);
    }
}
