<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\processManager\phoneToBook\jobs\AfterBookedFlightJob;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class AfterBookedFlightOrderProcessListener
 *
 * @property OrderProcessManagerRepository $repository
 */
class AfterBookedFlightOrderProcessListener
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
//            \Yii::info([
//                'message' => 'Not found Quote',
//                'quoteId' => $event->productQuoteId,
//            ], 'info\OrderProcessManager:AfterBookedFlightOrderProcessListener');
            return;
        }

        if (!$quote->pq_order_id) {
//            \Yii::info([
//                'message' => 'Quote has not relation with Order',
//                'quoteId' => $event->productQuoteId,
//            ], 'info\OrderProcessManager:AfterBookedFlightOrderProcessListener');
            return;
        }

        if (!$quote->isFlight()) {
//            \Yii::info([
//                'message' => 'Quote is not flight type',
//                'quoteId' => $event->productQuoteId,
//            ], 'info\OrderProcessManager:AfterBookedFlightOrderProcessListener');
            return;
        }

        $process = $this->repository->get($quote->pq_order_id);

        if (!$process) {
            return;
        }

        if (!$process->isRunning()) {
            \Yii::info([
                'message' => 'Order Process is not Running',
                'orderId' => $quote->pq_order_id,
            ], 'info\OrderProcessManager:AfterBookedQuoteListener');
            return;
        }

        \Yii::$app->queue_job->push(new AfterBookedFlightJob($event->productQuoteId));
    }
}
