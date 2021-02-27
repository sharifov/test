<?php

namespace modules\order\src\processManager\jobs;

use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\processManager\OrderProcessManagerRepository;
use yii\queue\RetryableJobInterface;

/**
 * Class BookingFlightJob
 *
 * @property $quoteId
 */
class BookingFlightJob implements RetryableJobInterface
{
    public $quoteId;

    public function __construct(int $quoteId)
    {
        $this->quoteId = $quoteId;
    }

    public function execute($queue)
    {
        \Yii::info([
            'message' => 'Booking Flight Quote processing',
            'quoteId' => $this->quoteId,
        ], 'info\OrderProcessManager:BookingFlightJob');
    }

    public function getTtr(): int
    {
        return 5;
    }

    public function canRetry($attempt, $error): bool
    {
        \Yii::error([
            'attempt' => $attempt,
            'message' => 'Booking Flight error',
            'error' => $error->getMessage(),
        ], 'OrderProcessManager:BookingFlightJob');
        return false;
        return !($attempt > 5);
    }
}
