<?php

namespace modules\hotel\src\jobs;

use modules\hotel\models\HotelQuote;
use modules\hotel\src\services\hotelQuote\HotelQuotePdfService;
use modules\order\src\events\OrderFileGeneratedEvent;
use Yii;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;
use src\helpers\app\AppHelper;
use src\repositories\NotFoundException;

/**
 * Class HotelQuotePdfJob
 * @property int $hotelQuoteId
 */
class HotelQuotePdfJob implements RetryableJobInterface
{
    public $hotelQuoteId;

    /**
     * @param Queue $queue
     * @throws \Exception
     */
    public function execute($queue): void
    {
        \Yii::info([
            'message' => 'HotelQuotePdfJob is started',
            'quoteId' => $this->hotelQuoteId,
        ], 'info\HotelQuotePdfJob:run');

        try {
            if (!$hotelQuote = HotelQuote::findOne(['hq_id' => $this->hotelQuoteId])) {
                throw new NotFoundException('HotelQuote not found. Id (' . $this->hotelQuoteId . ')');
            }
            HotelQuotePdfService::guard($hotelQuote);
            $hotelQuotePdfService = new HotelQuotePdfService($hotelQuote);
            $hotelQuotePdfService->setProductQuoteId($hotelQuote->hq_product_quote_id);
            if ($hotelQuotePdfService->processingFile()) {
                \Yii::info([
                    'message' => 'HotelQuotePdfJob - file is generated',
                    'quoteId' => $this->hotelQuoteId,
                ], 'info\HotelQuotePdfJob:success');
            }
        } catch (NotFoundException $throwable) {
            $log = AppHelper::throwableLog($throwable);
            $log['quoteId'] = $this->hotelQuoteId;
            Yii::error($log, 'HotelQuotePdfJob:Execute:NotFoundException');
        } catch (\Throwable $throwable) {
            $log = AppHelper::throwableLog($throwable);
            $log['quoteId'] = $this->hotelQuoteId;
            Yii::error($log, 'HotelQuotePdfJob:Execute:Throwable');
            throw new \Exception($throwable->getMessage());
        }
    }

    /**
     * @return int time to reserve in seconds
     */
    public function getTtr(): int
    {
        return 2 * 60;
    }

    /**
     * @param int $attempt number
     * @param \Exception|\Throwable $error from last execute of the job
     * @return bool
     */
    public function canRetry($attempt, $error): bool
    {
        return ($attempt < 3);
    }
}
