<?php

namespace modules\order\src\processManager\phoneToBook\jobs;

use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Class StartBookingJob
 *
 * @property $orderId
 */
class StartBookingJob implements JobInterface
{
    public $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        $repo = \Yii::createObject(OrderProcessManagerRepository::class);

        $process = $repo->get($this->orderId);

        if (!$process) {
            \Yii::error([
                'message' => 'Not found Order Process Manager',
                'processOrderId' => $this->orderId,
            ], 'OrderProcessManager:StartBookingJob');
            return;
        }

        try {
            $process->bookingFlight(new \DateTimeImmutable());
            $repo->save($process);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Transfer OrderProcess to booking flight error',
                'error' => $e->getMessage(),
                'orderId' => $this->orderId,
            ], 'StartBookingJob');
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
//            'message' => 'Order Process manager cant to BookingFlight',
//            'error' => $error->getMessage(),
//        ], 'OrderProcessManager:StartBookingJob');
//        return !($attempt > 5);
//    }
}
