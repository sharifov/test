<?php

namespace modules\order\src\processManager\jobs;

use modules\flight\components\api\FlightQuoteBookService;
use modules\flight\models\FlightQuote;
use modules\flight\src\services\flightQuote\FlightQuoteBookGuardService;
use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\processManager\OrderProcessManagerRepository;
use modules\product\src\entities\productQuote\ProductQuoteRepository;
use yii\helpers\ArrayHelper;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;

/**
 * Class BookingFlightJob
 *
 * @property $quoteId
 */
class BookingFlightJob implements JobInterface
{
    public $quoteId;

    public function __construct(int $flightQuoteId)
    {
        $this->quoteId = $flightQuoteId;
    }

    public function execute($queue)
    {
        \Yii::info([
            'message' => 'Booking Flight Quote processing',
            'quoteId' => $this->quoteId,
        ], 'info\OrderProcessManager:BookingFlightJob');

        if (!$flightQuote = FlightQuote::findOne($this->quoteId)) {
            \Yii::error([
                'message' => 'Not found Flight Quote',
                'quoteId' => $this->quoteId,
            ], 'OrderProcessManager:BookingFlightJob');
            return;
        }

        try {
            FlightQuoteBookGuardService::guard($flightQuote);
            $requestData = ArrayHelper::getValue($flightQuote, 'fqProductQuote.pqOrder.or_request_data');
            $responseData = FlightQuoteBookService::requestBook($requestData);
            FlightQuoteBookService::createBook($flightQuote, $responseData);
        } catch (\Throwable $e) {
            $this->errorBook($flightQuote);
            \Yii::error([
                'message' => 'Booking Flight error',
                'error' => $e->getMessage(),
                'quoteId' => $this->quoteId,
            ], 'OrderProcessManager:BookingFlightJob');

            if ($userId = ($flightQuote->fqProductQuote->pqOrder->orLead->employee_id ?? null)) {
                Notifications::createAndPublish(
                    $userId,
                    'Booking Flight error.',
                    'QuoteId: ' . $this->quoteId . ' Error: ' . $e->getMessage(),
                    Notifications::TYPE_DANGER,
                    true
                );
            }
        }
    }

    private function errorBook(FlightQuote $flightQuote): void
    {
        try {
            $productQuoteRepository = \Yii::createObject(ProductQuoteRepository::class);
            $productQuote = $flightQuote->fqProductQuote;
            $productQuote->error(null, 'Auto booking error');
            $productQuoteRepository->save($productQuote);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Flight Quote Transfer to "Error" error',
                'flightQuoteId' => $flightQuote->fq_id,
            ], 'OrderProcessManager:BookingFlightJob');
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
//            'message' => 'Booking Flight error',
//            'error' => $error->getMessage(),
//        ], 'OrderProcessManager:BookingFlightJob');
//        return false;
//        return !($attempt > 5);
//    }
}
