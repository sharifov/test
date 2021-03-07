<?php

namespace modules\order\src\processManager\jobs;

use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use modules\attraction\models\AttractionQuote;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;
use Yii;

/**
 * Class BookingAttractionJob
 *
 * @property $quoteId
 */
class BookingAttractionJob implements JobInterface
{
    public $quoteId;

    public function __construct(int $quoteId)
    {
        $this->quoteId = $quoteId;
    }

    public function execute($queue)
    {
        \Yii::info([
            'message' => 'Booking Attraction Quote processing',
            'quoteId' => $this->quoteId,
        ], 'info\OrderProcessManager:BookingAttractionJob');

        $model = AttractionQuote::findOne($this->quoteId);

        if (!$model) {
            \Yii::error([
                'message' => 'Not found Attraction Quote',
                'quoteId' => $this->quoteId,
            ], 'OrderProcessManager:BookingAttractionJob');
            return;
        }

        try {
            $model->atnq_booking_id = strtoupper(substr(md5(mt_rand()), 0, 7));
            $model->save();
            $productQuote = $model->atnqProductQuote;
            $productQuote->booked();
            $repo = Yii::createObject(ProductQuoteRepository::class);
            $repo->save($productQuote);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Booking Attraction error',
                'error' => $e->getMessage(),
                'quoteId' => $this->quoteId,
            ], 'OrderProcessManager:BookingAttractionJob');
            $userId = $model->atnqProductQuote->pqOrder->orLead->employee_id ?? null;
            if ($userId) {
                if ($ntf = Notifications::create($userId, 'Booking Attraction error.', 'QuoteId: ' . $this->quoteId . ' Error: ' . $e->getMessage(), Notifications::TYPE_DANGER, true)) {
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
//            'message' => 'Booking Attraction error',
//            'error' => $error->getMessage(),
//            'quoteId' => $this->quoteId,
//        ], 'OrderProcessManager:BookingAttractionJob');
//        return false;
//        return !($attempt > 5);
//    }
}
