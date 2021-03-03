<?php

namespace modules\flight\src\jobs;

use modules\flight\models\FlightQuote;
use modules\flight\src\services\flightQuote\FlightQuotePdfService;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;
use sales\helpers\app\AppHelper;
use sales\repositories\NotFoundException;

/**
 * Class FlightQuotePdfJob
 * @property int $flightQuoteId
 */
class FlightQuotePdfJob implements RetryableJobInterface
{
    public $flightQuoteId;

    /**
     * @param Queue $queue
     * @throws \Exception
     */
    public function execute($queue): void
    {
        \Yii::info([
            'message' => 'FlightQuotePdfJob is started',
            'quoteId' => $this->flightQuoteId,
        ], 'info\FlightQuotePdfJob:run');

        try {
            if (!$flightQuote = FlightQuote::findOne(['fq_id' => $this->flightQuoteId])) {
                throw new NotFoundException('FlightQuote not found. Id (' . $this->flightQuoteId . ')');
            }
            FlightQuotePdfService::processingFile($flightQuote);
        } catch (NotFoundException $throwable) {
            AppHelper::throwableLogger(
                $throwable,
                'FlightQuotePdfJob:Execute:Throwable'
            );
        } catch (\Throwable $throwable) {
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
