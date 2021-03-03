<?php

namespace modules\order\src\processManager\jobs;

use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\processManager\OrderProcessManagerRepository;
use yii\queue\RetryableJobInterface;

/**
 * Class StartAutoProcessingJob
 *
 * @property $orderId
 */
class StartAutoProcessingJob implements RetryableJobInterface
{
    public $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        if (OrderProcessManager::find()->andWhere(['opm_id' => $this->orderId])->exists()) {
            \Yii::error([
                'message' => 'Order Process Manager is already exist.',
                'orderId' => $this->orderId
            ], 'OrderProcessManager:StartAutoProcessingJob');
            return;
        }
        $repo = \Yii::createObject(OrderProcessManagerRepository::class);
        $process = OrderProcessManager::create($this->orderId, new \DateTimeImmutable());
        $repo->save($process);
    }

    public function getTtr(): int
    {
        return 1 * 60;
    }

    public function canRetry($attempt, $error): bool
    {
        \Yii::error([
            'attempt' => $attempt,
            'message' => 'Order Process Manager Start error',
            'error' => $error->getMessage(),
        ], 'OrderProcessManager:StartAutoProcessingJob');
        return !($attempt > 5);
    }
}
