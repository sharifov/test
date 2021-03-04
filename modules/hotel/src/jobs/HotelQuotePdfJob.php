<?php

namespace modules\hotel\src\jobs;

use modules\hotel\models\HotelQuote;
use modules\hotel\src\services\hotelQuote\HotelQuotePdfService;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;
use sales\helpers\app\AppHelper;
use sales\repositories\NotFoundException;

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
            if (HotelQuotePdfService::processingFile($hotelQuote)) {
                \Yii::info([
                    'message' => 'HotelQuotePdfJob - file is generated',
                    'quoteId' => $this->hotelQuoteId,
                ], 'info\HotelQuotePdfJob:success');
            }
        } catch (NotFoundException $throwable) {
            AppHelper::throwableLogger(
                $throwable,
                'HotelQuotePdfJob:Execute:Throwable'
            );
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger(
                $throwable,
                'HotelQuotePdfJob:Execute:Throwable'
            );
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
