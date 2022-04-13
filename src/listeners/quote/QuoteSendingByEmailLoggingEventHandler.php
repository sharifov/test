<?php

namespace src\listeners\quote;

use common\models\QuoteCommunication;
use frontend\models\CommunicationForm;
use src\events\quote\QuoteSendByEmailLoggingEvent;
use src\helpers\app\AppHelper;

/**
 * Class QuoteSendingByEmailLoggingEventHandler
 * @package src\listeners\quote
 */
class QuoteSendingByEmailLoggingEventHandler
{
    /**
     * @param QuoteSendByEmailLoggingEvent $event
     */
    public function handle(QuoteSendByEmailLoggingEvent $event): void
    {
        try {
            $model = new QuoteCommunication([
                'qc_communication_type' => CommunicationForm::TYPE_EMAIL,
                'qc_communication_id' => $event->emailId,
                'qc_quote_id' => $event->quoteId
            ]);
            $model->save();
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'QuoteSendingByEmailLoggingEventHandler:Throwable', true);
        }
    }
}
