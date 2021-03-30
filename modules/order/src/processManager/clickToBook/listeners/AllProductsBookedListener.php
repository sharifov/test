<?php

namespace modules\order\src\processManager\clickToBook\listeners;

use modules\order\src\processManager\AllProductsBookedChecker;
use modules\order\src\processManager\clickToBook\jobs\BookManagerJob;
use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\queue\Queue;
use modules\product\src\entities\productQuote\events\ProductQuoteBookedEvent;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class AllProductsBookedListener
 *
 * @property OrderProcessManagerRepository $repository
 * @property AllProductsBookedChecker $allProductsBookedChecker
 * @property Queue $queue
 */
class AllProductsBookedListener
{
    private OrderProcessManagerRepository $repository;
    private AllProductsBookedChecker $allProductsBookedChecker;
    private Queue $queue;

    public function __construct(
        OrderProcessManagerRepository $repository,
        AllProductsBookedChecker $allProductsBookedChecker,
        Queue $queue
    ) {
        $this->repository = $repository;
        $this->allProductsBookedChecker = $allProductsBookedChecker;
        $this->queue = $queue;
    }

    public function handle(ProductQuoteBookedEvent $event): void
    {
        $quote = ProductQuote::findOne($event->productQuoteId);

        if (!$quote) {
            return;
        }

        if (!$quote->pq_order_id) {
            return;
        }

        $manager = $this->repository->get($quote->pq_order_id);

        if (!$manager) {
            return;
        }

        if (!$manager->isFlightProductProcessed()) {
            return;
        }

        if ($this->allProductsBookedChecker->isBooked($quote->pq_order_id)) {
            $this->queue->push(new BookManagerJob($quote->pq_order_id));
        }
    }
}
