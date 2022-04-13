<?php

namespace src\listeners\quote;

use common\models\QuoteCommunication;
use frontend\models\CommunicationForm;
use src\events\quote\QuoteSendBySmsLoggingEvent;
use src\helpers\app\AppHelper;

/**
 * Class QuoteSendingBySmsLoggingEventHandler
 * @package src\listeners\quote
 */
class QuoteSendingBySmsLoggingEventHandler
{
    /**
     * @param QuoteSendBySmsLoggingEvent $event
     */
    public function handle(QuoteSendBySmsLoggingEvent $event): void
    {
        try {
            $model = new QuoteCommunication([
                'qc_communication_type' => CommunicationForm::TYPE_SMS,
                'qc_communication_id' => $event->smsId,
                'qc_quote_id' => $event->quoteId
            ]);
            $model->save();
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'QuoteSendingBySmsLoggingEventHandler:Throwable', true);
        }
    }
}
