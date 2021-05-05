<?php

namespace modules\order\src\processManager\clickToBook\jobs;

use common\models\Lead;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use modules\hotel\models\HotelQuote;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteBookGuard;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteBookService;
use modules\hotel\src\useCases\api\bookQuote\HotelQuoteCheckRateService;
use modules\lead\src\services\LeadFailBooking;
use modules\order\src\processManager\clickToBook\ErrorOrder;
use sales\repositories\lead\LeadRepository;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;
use Yii;

/**
 * Class BookingHotelJob
 *
 * @property $quoteId
 */
class BookingHotelJob implements JobInterface
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


        $quote = HotelQuote::findOne($this->quoteId);

        if (!$quote) {
            \Yii::error([
                'message' => 'Not found Hotel Quote',
                'quoteId' => $this->quoteId,
            ], 'OrderProcessManager:BookingHotelJob');
            return;
        }

        try {
            HotelQuoteBookGuard::guard($quote);

            /** @var HotelQuoteBookService $bookService */
            $bookService = Yii::$container->get(HotelQuoteBookService::class);

            /** @var HotelQuoteCheckRateService $checkRateService */
            $checkRateService = Yii::$container->get(HotelQuoteCheckRateService::class);
            $checkResult = $checkRateService->checkRateByHotelQuote($quote);
            if ($checkResult->status) {
                $bookService->book($quote);
                if ($bookService->status) {
                    return;
                }
                throw new \DomainException($bookService->message);
            }
            throw new \DomainException($checkResult->message);
        } catch (\Throwable $e) {
            $this->errorBook($quote);
            \Yii::error([
                'message' => 'Booking Hotel error',
                'error' => $e->getMessage(),
                'quoteId' => $this->quoteId,
            ], 'OrderProcessManager:BookingHotelJob');
            $userId = $quote->hqProductQuote->pqOrder->orLead->employee_id ?? null;
            if ($userId) {
                if ($ntf = Notifications::create($userId, 'Booking Hotel error.', 'QuoteId: ' . $this->quoteId . ' Error: ' . $e->getMessage(), Notifications::TYPE_DANGER, true)) {
                    Notifications::publish('getNewNotification', ['user_id' => $userId], NotificationMessage::add($ntf));
                }
            }
        }
    }

    private function errorBook(HotelQuote $quote): void
    {
        try {
            $orderId = $quote->hqProductQuote->pq_order_id ?? null;
            if ($orderId) {
                $errorOrder = Yii::createObject(ErrorOrder::class);
                $errorOrder->error($orderId, 'ClickToBook AutoProcessing Error: Hotel Quote Book Error');
            }
            $this->createBookFailedLead($quote->hq_product_quote_id);
        } catch (\Throwable $e) {
            Yii::error([
                'message' => 'BookingHotelJob errorBook',
                'error' => $e->getMessage(),
                'quoteId' => $quote->hq_id,
            ], 'BookingHotelJob:errorBook');
        }
    }

    private function createBookFailedLead(int $productQuoteId): void
    {
        $leadFailBookingService = Yii::createObject(LeadFailBooking::class);
        $leadFailBookingService->create($productQuoteId, null);
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
//            'message' => 'Booking Hotel error',
//            'error' => $error->getMessage(),
//            'quoteId' => $this->quoteId,
//        ], 'OrderProcessManager:BookingHotelJob');
//        return false;
//        return !($attempt > 5);
//    }
}
