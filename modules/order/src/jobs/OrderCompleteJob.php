<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\OrderRepository;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderCompleteJob
 *
 * @property int $orderId
 */
class OrderCompleteJob implements RetryableJobInterface
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
        $order->complete(new \DateTimeImmutable());
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
            'message' => 'Order complete error',
            'error' => $error->getMessage(),
            'orderId' => $this->orderId,
        ], 'OrderCompleteJob');
        return !($attempt > 5);
    }
}
