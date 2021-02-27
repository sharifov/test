<?php

namespace modules\order\src\listeners\order;

use modules\order\src\jobs\OrderPrepareJob;
use modules\order\src\processManager\OrderProcessManager;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\ProductQuote;

class OrderPrepareListener
{
    public function handle(ProductQuoteBookedEvent $event): void
    {
        $quote = ProductQuote::findOne($event->productQuoteId);

        if (!$quote) {
            \Yii::error([
                'message' => 'Not found Quote',
                'quoteId' => $event->productQuoteId,
            ], 'OrderPrepareListener');
            return;
        }

        if (!$quote->pq_order_id) {
            \Yii::error([
                'message' => 'Quote has not relation with Order',
                'quoteId' => $event->productQuoteId,
            ], 'OrderPrepareListener');
            return;
        }

        $process = OrderProcessManager::findOne($quote->pq_order_id);

        if ($process) {
            return;
        }

        $order = $quote->pqOrder;

        if (!$order) {
            \Yii::error([
                'message' => 'Not found Order',
                'quoteId' => $quote->pq_id,
                'orderId' => $quote->pq_order_id,
            ], 'OrderPrepareListener');
            return;
        }

        $quotes = $order->productQuotes;

        if (!$quotes) {
            \Yii::error([
                'message' => 'Not found Quotes for Order',
                'quoteId' => $event->productQuoteId,
                'orderId' => $order->or_id,
            ], 'OrderPrepareListener');
            return;
        }

        foreach ($quotes as $quote) {
            if (!$quote->isBooked()) {
                return;
            }
        }

        \Yii::$app->queue_job->push(new OrderPrepareJob($order->or_id));
    }
}
