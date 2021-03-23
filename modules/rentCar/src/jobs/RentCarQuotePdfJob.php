<?php

namespace modules\rentCar\src\jobs;

use modules\order\src\events\OrderFileGeneratedEvent;
use modules\rentCar\src\entity\rentCarQuote\RentCarQuote;
use modules\rentCar\src\services\RentCarQuotePdfService;
use yii\queue\Queue;
use yii\queue\RetryableJobInterface;
use sales\helpers\app\AppHelper;
use sales\repositories\NotFoundException;

/**
 * Class RentCarQuotePdfJob
 * @property int $rentCarQuoteId
 */
class RentCarQuotePdfJob implements RetryableJobInterface
{
    public $rentCarQuoteId;

    /**
     * @param Queue $queue
     * @throws \Exception
     */
    public function execute($queue): void
    {
        \Yii::info([
            'message' => 'RentCarQuotePdfJob is started',
            'quoteId' => $this->rentCarQuoteId,
        ], 'info\RentCarQuotePdfJob:run');

        try {
            if (!$rentCarQuote = RentCarQuote::findOne(['rcq_id' => $this->rentCarQuoteId])) {
                throw new NotFoundException('RentCarQuote not found. Id (' . $this->rentCarQuoteId . ')');
            }
            $rentCarQuotePdfService = new RentCarQuotePdfService($rentCarQuote);
            $rentCarQuotePdfService->setProductQuoteId($rentCarQuote->rcqProductQuote->pq_id);
            if ($rentCarQuotePdfService->processingFile()) {
                \Yii::info([
                    'message' => 'RentCarQuotePdfJob - file is generated',
                    'quoteId' => $this->rentCarQuoteId,
                ], 'info\RentCarQuotePdfJob:success');
            }
        } catch (NotFoundException $throwable) {
            AppHelper::throwableLogger(
                $throwable,
                'RentCarQuotePdfJob:Execute:Throwable'
            );
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger(
                $throwable,
                'RentCarQuotePdfJob:Execute:Throwable'
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
