<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\processManager\jobs\ProcessManagerBookedJob;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class AfterBookedQuoteOrderProcessListener
 *
 * @property OrderProcessManagerRepository $repository
 */
class AfterBookedQuoteOrderProcessListener
{
    private OrderProcessManagerRepository $repository;

    public function __construct(OrderProcessManagerRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(ProductQuoteBookedEvent $event): void
    {
        $quote = ProductQuote::findOne($event->productQuoteId);

        if (!$quote) {
            \Yii::error([
                'message' => 'Not found Quote',
                'quoteId' => $event->productQuoteId,
            ], 'OrderProcessManager:AfterBookedQuoteOrderProcessListener');
            return;
        }

        if (!$quote->pq_order_id) {
            \Yii::error([
                'message' => 'Quote has not relation with Order',
                'quoteId' => $event->productQuoteId,
            ], 'OrderProcessManager:AfterBookedQuoteOrderProcessListener');
            return;
        }

        if ($quote->isFlight()) {
            return;
        }

        $process = $this->repository->get($quote->pq_order_id);

        if (!$process) {
//            \Yii::info([
//                'message' => 'Not found Order Process Manager',
//                'orderId' => $quote->pq_order_id,
//            ], 'info\OrderProcessManager:AfterBookedQuoteListener');
            return;
        }

        if (!$process->isOtherProductsBooking()) {
            \Yii::error([
                'message' => 'Order Process Manager is not in Other Products Booking. Status Id: ' . $process->opm_status,
                'quoteId' => $quote->pq_id,
                'orderId' => $quote->pq_order_id,
            ], 'OrderProcessManager:AfterBookedQuoteOrderProcessListener');
            return;
        }

        $order = $quote->pqOrder;

        if (!$order) {
            \Yii::error([
                'message' => 'Not found Order',
                'quoteId' => $quote->pq_id,
                'orderId' => $quote->pq_order_id,
            ], 'OrderProcessManager:AfterBookedQuoteOrderProcessListener');
            return;
        }

        $quotes = $order->productQuotes;

        if (!$quotes) {
            \Yii::error([
                'message' => 'Not found Quotes for Order',
                'quoteId' => $event->productQuoteId,
                'orderId' => $order->or_id,
            ], 'OrderProcessManager:AfterBookedQuoteOrderProcessListener');
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
