<?php

namespace modules\order\src\processManager\jobs;

use modules\flight\components\api\FlightQuoteBookService;
use modules\flight\models\FlightQuote;
use modules\flight\src\services\flightQuote\FlightQuoteBookGuardService;
use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\processManager\OrderProcessManagerRepository;
use yii\helpers\ArrayHelper;
use yii\queue\RetryableJobInterface;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;

/**
 * Class BookingFlightJob
 *
 * @property $quoteId
 */
class BookingFlightJob implements RetryableJobInterface
{
    public $quoteId;

    public function __construct(int $flightQuoteId)
    {
        $this->quoteId = $flightQuoteId;
    }

    public function execute($queue)
    {
        sleep(5);
        \Yii::error("test");
        die;
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

    public function getTtr(): int
    {
        return 1 * 60;
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
