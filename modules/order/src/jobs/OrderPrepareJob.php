<?php

namespace modules\order\src\jobs;

use modules\order\src\entities\order\OrderRepository;
use modules\order\src\entities\order\OrderStatusAction;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Class OrderPrepareJob
 *
 * @property int $orderId
 */
class OrderPrepareJob implements JobInterface
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
            $order->prepare('', OrderStatusAction::JOB, null);
            $repo->save($order);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Cant transfer order to prepare',
                'error' => $e->getMessage(),
                'orderId' => $this->orderId,
            ], 'OrderPrepareJob');
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
//            'message' => 'Order prepare error',
//            'error' => $error->getMessage(),
//            'orderId' => $this->orderId,
//        ], 'OrderPrepareJob');
//        return !($attempt > 5);
//    }
}
