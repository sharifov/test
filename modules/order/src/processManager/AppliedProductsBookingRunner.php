<?php

namespace modules\order\src\processManager;

use modules\order\src\processManager\jobs\BookingHotelJob;
use modules\product\src\entities\productQuote\ProductQuote;

/**
 * Class AppliedProductsBookingRunner
 *
 * @property Queue $queue
 */
class AppliedProductsBookingRunner
{
    private Queue $queue;

    public function __construct(Queue $queue)
    {
        $this->queue = $queue;
    }

    public function run(int $orderId): void
    {
        $quotes = ProductQuote::find()->byOrderId($orderId)->applied()->all();

        foreach ($quotes as $quote) {
            if ($quote->pqProduct->isHotel()) {
                $this->queue->push(new BookingHotelJob($quote->childQuote->getId()));
            }
        }
    }
}