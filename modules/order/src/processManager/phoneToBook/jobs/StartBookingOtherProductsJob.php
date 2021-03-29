<?php

namespace modules\order\src\processManager\phoneToBook\jobs;

use modules\order\src\entities\order\Order;
use modules\order\src\processManager\jobs\BookingAttractionJob;
use modules\order\src\processManager\jobs\BookingHotelJob;
use modules\order\src\processManager\jobs\BookingRentCarJob;
use modules\order\src\processManager\queue\Queue;
use yii\queue\JobInterface;

/**
 * Class StartBookingOtherProductsJob
 *
 * @property $orderId
 */
class StartBookingOtherProductsJob implements JobInterface
{
    public $orderId;

    public function __construct(int $orderId)
    {
        $this->orderId = $orderId;
    }

    public function execute($queue)
    {
        $order = Order::findOne($this->orderId);

        if (!$order) {
            \Yii::error([
                'message' => 'Not found Order',
                'orderId' => $this->orderId,
            ], 'StartBookingOtherProductsJob');
            return;
        }

        $quotes = $order->productQuotes;

        if (!$quotes) {
            \Yii::error([
                'message' => 'Not found Quotes for Order',
                'orderId' => $this->orderId,
            ], 'StartBookingOtherProductsJob');
            return;
        }

        $queueJob = \Yii::createObject(Queue::class);

        $createdAnyJob = false;
        foreach ($quotes as $quote) {
            if ($quote->isBooked()) {
                continue;
            }
            if ($quote->pqProduct->isHotel()) {
                $queueJob->push(new BookingHotelJob($quote->childQuote->getId()));
                $createdAnyJob = true;
            } elseif ($quote->pqProduct->isAttraction()) {
                $queueJob->push(new BookingAttractionJob($quote->childQuote->getId()));
                $createdAnyJob = true;
            } elseif ($quote->pqProduct->isRenTCar()) {
                $queueJob->push(new BookingRentCarJob($quote->childQuote->getId()));
                $createdAnyJob = true;
            }
        }

        if (!$createdAnyJob) {
            \Yii::error([
                'message' => 'Not created Booking Job',
                'orderId' => $this->orderId,
            ], 'StartBookingOtherProductsJob');
        }
    }
}
