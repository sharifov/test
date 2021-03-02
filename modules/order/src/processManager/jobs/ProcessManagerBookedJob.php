<?php

namespace modules\order\src\processManager\jobs;

use modules\order\src\processManager\OrderProcessManagerRepository;
use yii\queue\RetryableJobInterface;

/**
 * Class ProcessManagerBookedJob
 *
 * @property int $orderId
 */
class ProcessManagerBookedJob implements RetryableJobInterface
{
    public $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        $repo = \Yii::createObject(OrderProcessManagerRepository::class);
        $process = $repo->find($this->orderId);
        $process->booked(new \DateTimeImmutable());
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
            'message' => 'Order Process manager booked error',
            'orderId' => $this->orderId,
            'error' => $error->getMessage(),
        ], 'OrderProcessManager:ProcessManagerBookedJob');
        return !($attempt > 5);
    }
}
