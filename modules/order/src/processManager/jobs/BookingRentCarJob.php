<?php

namespace modules\order\src\processManager\jobs;

use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\rentCar\src\services\RentCarQuoteBookGuard;
use modules\rentCar\src\services\RentCarQuoteBookService;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Class BookingRentCarJob
 *
 * @property $quoteId
 */
class BookingRentCarJob implements JobInterface
{
    public $quoteId;

    public function __construct(int $quoteId)
    {
        $this->quoteId = $quoteId;
    }

    public function execute($queue)
    {
        \Yii::info([
            'message' => 'Booking RentCar Quote processing',
            'quoteId' => $this->quoteId,
        ], 'info\OrderProcessManager:BookingRentCarJob');

        $quote = RentCarQuote::findOne($this->quoteId);

        if (!$quote) {
            \Yii::error([
                'message' => 'Not found RentCar Quote',
                'quoteId' => $this->quoteId,
            ], 'OrderProcessManager:BookingRentCarJob');
            return;
        }

        try {
            RentCarQuoteBookGuard::guard($quote);
            RentCarQuoteBookService::book($quote, null);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Booking RentCar error',
                'error' => $e->getMessage(),
                'quoteId' => $this->quoteId,
            ], 'OrderProcessManager:BookingRentCarJob');
            $userId = $quote->rcqProductQuote->pqOrder->orLead->employee_id ?? null;
            if ($userId) {
                if ($ntf = Notifications::create($userId, 'Booking RentCar error.', 'QuoteId: ' . $this->quoteId . ' Error: ' . $e->getMessage(), Notifications::TYPE_DANGER, true)) {
                    Notifications::publish('getNewNotification', ['user_id' => $userId], NotificationMessage::add($ntf));
                }
            }
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
//            'message' => 'Booking RentCar error',
//            'error' => $error->getMessage(),
//            'quoteId' => $this->quoteId,
//        ], 'OrderProcessManager:BookingRentCarJob');
//        return false;
//        return !($attempt > 5);
//    }
}
