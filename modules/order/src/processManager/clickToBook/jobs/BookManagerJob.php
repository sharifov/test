<?php

namespace modules\order\src\processManager\clickToBook\jobs;

use modules\order\src\processManager\clickToBook\commands;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Class BookManagerJob
 *
 * @property $orderId
 */
class BookManagerJob implements JobInterface
{
    public $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        try {
            $handler = \Yii::createObject(commands\bookManager\Handler::class);
            $handler->handle(new commands\bookManager\Command($this->orderId));
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'ClickToBook OrderProcess manager book error',
                'error' => $e->getMessage(),
                'orderId' => $this->orderId,
            ], 'BookManagerJob');
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
//            'message' => 'Order Process Manager Start error',
//            'error' => $error->getMessage(),
//        ], 'OrderProcessManager:CompleteOrderJob');
//        return !($attempt > 5);
//    }
}
