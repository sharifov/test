<?php

namespace modules\order\src\processManager\phoneToBook\jobs;

use modules\order\src\processManager\phoneToBook\OrderProcessManager;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Class StartAutoProcessingJob
 *
 * @property $orderId
 */
class StartAutoProcessingJob implements JobInterface
{
    public $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        $repo = \Yii::createObject(OrderProcessManagerRepository::class);

        if ($repo->exist($this->orderId)) {
            \Yii::error([
                'message' => 'Order Process Manager is already exist.',
                'orderId' => $this->orderId
            ], 'OrderProcessManager:StartAutoProcessingJob');
            return;
        }

        try {
            $process = OrderProcessManager::create($this->orderId, new \DateTimeImmutable());
            $repo->save($process);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'OrderProcess manager create error',
                'error' => $e->getMessage(),
                'orderId' => $this->orderId,
            ], 'StartAutoProcessingJob');
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
//        ], 'OrderProcessManager:StartAutoProcessingJob');
//        return !($attempt > 5);
//    }
}
