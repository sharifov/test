<?php

namespace sales\listeners\quote;

use common\components\ga\GaHelper;
use common\components\jobs\SendQuoteInfoToGaJob;
use sales\events\quote\QuoteSendEvent;
use sales\helpers\app\AppHelper;
use Yii;
use yii\helpers\VarDumper;

/**
 * Class QuoteSendEventListener
 */
class QuoteSendEventListener
{
    /**
     * @param QuoteSendEvent $event
     */
    public function handle(QuoteSendEvent $event): void
    {
        try {
            $logData = [];
            $logData['event'] = 'QuoteSendAnalyticEvent';
            $logData['quote'] = $event->quote->attributes();
            $logData['lead'] = $event->quote->lead->attributes();
            Yii::info($logData, 'AS/*');

            if (GaHelper::checkSettings(GaHelper::TYPE_QUOTE) && $event->quote->lead->isReadyForGa()) {
                $job = new SendQuoteInfoToGaJob();
                $job->quoteId = $event->quote->id;
                Yii::$app->queue_job->priority(20)->push($job);
            }
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'QuoteSendEventListener:Throwable', true);
        }
    }
}
