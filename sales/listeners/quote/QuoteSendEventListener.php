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
            if (GaHelper::checkSettings(GaHelper::TYPE_QUOTE) && $event->quote->lead->isReadyForGa()) {
                $job = new SendQuoteInfoToGaJob();
                $job->quote = $event->quote;
                Yii::$app->queue_job->priority(20)->push($job);
            }
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'QuoteSendEventListener:Throwable', true);
        }
    }

}
