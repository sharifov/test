<?php

namespace modules\order\src\processManager\jobs;

use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\processManager\OrderProcessManagerRepository;
use yii\queue\RetryableJobInterface;

/**
 * Class BookingHotelJob
 *
 * @property $quoteId
 */
class BookingHotelJob implements RetryableJobInterface
{
    public $quoteId;

    public function __construct(int $quoteId)
    {
        $this->quoteId = $quoteId;
    }

    public function execute($queue)
    {
        \Yii::info([
            'message' => 'Booking Hotel Quote processing',
            'quoteId' => $this->quoteId,
        ], 'info\OrderProcessManager:BookingHotelJob');
    }

    public function getTtr(): int
    {
        return 5;
    }

    public function canRetry($attempt, $error): bool
    {
        \Yii::error([
            'attempt' => $attempt,
            'message' => 'Booking Hotel error',
            'error' => $error->getMessage(),
        ], 'OrderProcessManager:BookingHotelJob');
        return false;
        return !($attempt > 5);
    }
}
