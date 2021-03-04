<?php

namespace modules\attraction\src\jobs;

use modules\attraction\models\AttractionQuote;
use modules\attraction\src\services\AttractionQuotePdfService;
use yii\queue\RetryableJobInterface;
use sales\helpers\app\AppHelper;
use sales\repositories\NotFoundException;

class AttractionQuotePdfJob implements RetryableJobInterface
{
    public $quoteId;

    public function execute($queue): void
    {
        \Yii::info([
            'message' => 'AttractionQuotePdfJob is started',
            'quoteId' => $this->quoteId,
        ], 'info\AttractionQuotePdfJob:run');

        try {
            if (!$quote = AttractionQuote::findOne(['atnq_id' => $this->quoteId])) {
                throw new NotFoundException('AttractionQuote not found. Id (' . $this->quoteId . ')');
            }
            if (AttractionQuotePdfService::processingFile($quote)) {
                \Yii::info([
                    'message' => 'AttractionQuotePdfJob - file is generated',
                    'quoteId' => $this->quoteId,
                ], 'info\AttractionQuotePdfJob:success');
            }
        } catch (NotFoundException $throwable) {
            AppHelper::throwableLogger(
                $throwable,
                'AttractionQuotePdfJob:Execute:Throwable'
            );
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger(
                $throwable,
                'AttractionQuotePdfJob:Execute:Throwable'
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
