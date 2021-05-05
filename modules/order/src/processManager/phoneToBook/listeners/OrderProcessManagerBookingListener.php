<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\processManager\AllProductsBookedChecker;
use modules\order\src\processManager\jobs\ProcessManagerBookedJob;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\queue\Queue;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class AfterBookedQuoteOrderProcessListener
 *
 * @property OrderProcessManagerRepository $repository
 * @property Queue $queue
 * @property AllProductsBookedChecker $allProductsBookedChecker
 */
class OrderProcessManagerBookingListener
{
    private OrderProcessManagerRepository $repository;
    private Queue $queue;
    private AllProductsBookedChecker $allProductsBookedChecker;

    public function __construct(OrderProcessManagerRepository $repository, Queue $queue, AllProductsBookedChecker $allProductsBookedChecker)
    {
        $this->repository = $repository;
        $this->queue = $queue;
        $this->allProductsBookedChecker = $allProductsBookedChecker;
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

        $manager = $this->repository->get($quote->pq_order_id);

        if (!$manager) {
//            \Yii::info([
//                'message' => 'Not found Order Process Manager',
//                'orderId' => $quote->pq_order_id,
//            ], 'info\OrderProcessManager:AfterBookedQuoteListener');
            return;
        }

        if (!$manager->isOtherProductsBooking()) {
            \Yii::error([
                'message' => 'Order Process Manager is not in Other Products Booking. Status Id: ' . $manager->opm_status,
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

        if ($this->allProductsBookedChecker->isBooked($order->or_id)) {
            $this->queue->push(new ProcessManagerBookedJob($manager->opm_id));
        }
    }
}
