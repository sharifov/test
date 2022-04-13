<?php

namespace src\listeners\quote;

use common\models\QuoteCommunication;
use frontend\models\CommunicationForm;
use src\events\quote\QuoteSendByChatLoggingEvent;
use src\helpers\app\AppHelper;

/**
 * Class QuoteSendingBySmsLoggingEventHandler
 * @package src\listeners\quote
 */
class QuoteSendingByChatLoggingEventHandler
{
    /**
     * @param QuoteSendByChatLoggingEvent $event
     */
    public function handle(QuoteSendByChatLoggingEvent $event): void
    {
        try {
            $model = new QuoteCommunication([
                'qc_communication_type' => CommunicationForm::TYPE_CHAT,
                'qc_communication_id' => $event->chatId,
                'qc_quote_id' => $event->quoteId
            ]);
            $model->save();
        } catch (\Throwable $throwable) {
            AppHelper::throwableLogger($throwable, 'QuoteSendingByChatLoggingEventHandler:Throwable', true);
        }
    }
}
