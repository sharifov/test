<?php

namespace modules\order\src\processManager\clickToBook\jobs;

use modules\order\src\processManager\clickToBook\commands;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Class FlightProductProcessedErrorJob
 *
 * @property $orderId
 */
class FlightProductProcessedErrorJob implements JobInterface
{
    public $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        try {
            $handler = \Yii::createObject(commands\flightProductProcessedError\Handler::class);
            $handler->handle(new commands\flightProductProcessedError\Command($this->orderId));
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'ClickToBook OrderProcess manager Flight Product Processed Error error',
                'error' => $e->getMessage(),
                'orderId' => $this->orderId,
            ], 'FlightProductProcessedErrorJob');
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
//        ], 'OrderProcessManager:FlightProductProcessedErrorJob');
//        return !($attempt > 5);
//    }
}
