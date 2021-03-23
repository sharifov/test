<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\order\OrderStatusAction;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderCompleteJob
 *
 * @property int $orderId
 */
class OrderCompleteJob implements JobInterface
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
            $order->complete('', OrderStatusAction::JOB, null);
            $repo->save($order);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Cant transfer order to complete',
                'error' => $e->getMessage(),
                'orderId' => $this->orderId,
            ], 'OrderCompleteJob');
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
//            'message' => 'Order complete error',
//            'error' => $error->getMessage(),
//            'orderId' => $this->orderId,
//        ], 'OrderCompleteJob');
//        return !($attempt > 5);
//    }
}
