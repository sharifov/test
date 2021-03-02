<?php

namespace modules\order\src\processManager\jobs;

use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\processManager\OrderProcessManagerRepository;
use yii\queue\RetryableJobInterface;

/**
 * Class StartBookingJob
 *
 * @property $orderId
 */
class StartBookingJob implements RetryableJobInterface
{
    public $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        $process = OrderProcessManager::findOne($this->orderId);

        if (!$process) {
            \Yii::error([
                'message' => 'Not found Order Process Manager',
                'processOrderId' => $this->orderId,
            ], 'OrderProcessManager:StartBookingJob');
            return;
        }

        $repo = \Yii::createObject(OrderProcessManagerRepository::class);
        $process->bookingFlight(new \DateTimeImmutable());
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
            'message' => 'Order Process manager cant to BookingFlight',
            'error' => $error->getMessage(),
        ], 'OrderProcessManager:StartBookingJob');
        return !($attempt > 5);
    }
}
