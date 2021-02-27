<?php

namespace modules\order\src\processManager\listeners;

use modules\order\src\processManager\jobs\AfterBookedFlightJob;
use modules\order\src\processManager\OrderProcessManager;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\ProductQuote;

class AfterBookedFlightOrderProcessListener
{
    public function handle(ProductQuoteBookedEvent $event): void
    {
        $quote = ProductQuote::findOne($event->productQuoteId);

        if (!$quote) {
            \Yii::error([
                'message' => 'Not found Quote',
                'quoteId' => $event->productQuoteId,
            ], 'OrderProcessManager:AfterBookedFlightOrderProcessListener');
            return;
        }

        if (!$quote->pq_order_id) {
            \Yii::error([
                'message' => 'Quote has not relation with Order',
                'quoteId' => $event->productQuoteId,
            ], 'OrderProcessManager:AfterBookedFlightOrderProcessListener');
            return;
        }

        if (!$quote->isFlight()) {
            return;
        }

        $process = OrderProcessManager::findOne($quote->pq_order_id);
        if (!$process) {
//            \Yii::info([
//                'message' => 'Not found Order Process Manager',
//                'orderId' => $quote->pq_order_id,
//            ], 'info\OrderProcessManager:AfterBookedQuoteListener');
            return;
        }

        \Yii::$app->queue_job->push(new AfterBookedFlightJob($event->productQuoteId));
    }
}
