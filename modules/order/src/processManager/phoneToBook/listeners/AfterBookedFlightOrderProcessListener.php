<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\processManager\phoneToBook\jobs\AfterBookedFlightJob;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\queue\Queue;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class AfterBookedFlightOrderProcessListener
 *
 * @property OrderProcessManagerRepository $repository
 * @property Queue $queue
 */
class AfterBookedFlightOrderProcessListener
{
    private OrderProcessManagerRepository $repository;
    private Queue $queue;

    public function __construct(OrderProcessManagerRepository $repository, Queue $queue)
    {
        $this->repository = $repository;
        $this->queue = $queue;
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

        $manager = $this->repository->get($quote->pq_order_id);

        if (!$manager) {
            return;
        }

        if (!$manager->isRunning()) {
            \Yii::info([
                'message' => 'Order Process Manager is not Running',
                'orderId' => $quote->pq_order_id,
            ], 'info\OrderProcessManager:AfterBookedQuoteListener');
            return;
        }

        $this->queue->push(new AfterBookedFlightJob($event->productQuoteId));
    }
}
