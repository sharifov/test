<?php

namespace modules\order\src\processManager\listeners;

use modules\order\src\processManager\events\QuoteBookedEvent;
use modules\order\src\processManager\jobs\ProcessManagerBookedJob;
use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\processManager\OrderProcessManagerRepository;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class AfterBookedQuoteListener
 *
 * @property OrderProcessManagerRepository $orderProcessManagerRepository
 */
class AfterBookedQuoteListener
{
    private OrderProcessManagerRepository $orderProcessManagerRepository;

    public function __construct(OrderProcessManagerRepository $orderProcessManagerRepository)
    {
        $this->orderProcessManagerRepository = $orderProcessManagerRepository;
    }

    public function handle(QuoteBookedEvent $event): void
    {
        $quote = ProductQuote::findOne($event->quoteId);

        if (!$quote) {
            \Yii::error([
                'message' => 'Not found Quote',
                'quoteId' => $event->quoteId,
            ], 'OrderProcessManager:AfterBookedQuoteListener');
            return;
        }

        if (!$quote->pq_order_id) {
            \Yii::error([
                'message' => 'Quote has not relation with Order',
                'quoteId' => $event->quoteId,
            ], 'OrderProcessManager:AfterBookedQuoteListener');
            return;
        }

        $process = OrderProcessManager::findOne($quote->pq_order_id);
        if (!$process) {
            \Yii::error([
                'message' => 'Not found Order Process Manager',
                'orderId' => $quote->pq_order_id,
            ], 'OrderProcessManager:AfterBookedQuoteListener');
            return;
        }

        if (!$process->isOtherProductsBooking()) {
            \Yii::error([
                'message' => 'Order Process Manager is not in Other Products Booking. Status Id: ' . $process->opm_status,
                'quoteId' => $quote->pq_id,
                'orderId' => $quote->pq_order_id,
            ], 'OrderProcessManager:AfterBookedQuoteListener');
            return;
        }

        $order = $quote->pqOrder;

        if (!$order) {
            \Yii::error([
                'message' => 'Not found Order',
                'quoteId' => $quote->pq_id,
                'orderId' => $quote->pq_order_id,
            ], 'OrderProcessManager:AfterBookedQuoteListener');
            return;
        }

        $quotes = $order->productQuotes;

        if (!$quotes) {
            \Yii::error([
                'message' => 'Not found Quotes for Order',
                'quoteId' => $event->quoteId,
                'orderId' => $order->or_id,
            ], 'OrderProcessManager:AfterBookedQuoteListener');
            return;
        }

        foreach ($quotes as $quote) {
            if (!$quote->isBooked()) {
                return;
            }
        }

        \Yii::$app->queue_job->push(new ProcessManagerBookedJob($process->opm_id));
    }
}
