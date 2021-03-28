<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\entities\order\Order;
use modules\order\src\processManager\events\BookingFlightEvent;
use modules\order\src\processManager\jobs\BookingFlightJob;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository;

/**
 * Class BookingFlightListener
 *
 * @property OrderProcessManagerRepository $managerRepository
 */
class BookingFlightListener
{
    private OrderProcessManagerRepository $managerRepository;

    public function __construct(OrderProcessManagerRepository $managerRepository)
    {
        $this->managerRepository = $managerRepository;
    }

    public function handle(BookingFlightEvent $event): void
    {
        $order = Order::findOne($event->orderId);

        if (!$order) {
            \Yii::error([
                'message' => 'Not found Order',
                'orderId' => $event->orderId,
            ], 'BookingFlightListener');
            return;
        }

        if (!$this->managerRepository->exist($order->or_id)) {
            return;
        }

        $quotes = $order->productQuotes;

        if (!$quotes) {
            \Yii::error([
                'message' => 'Not found Quotes for Order',
                'orderId' => $event->orderId,
            ], 'BookingFlightListener');
            return;
        }

        foreach ($quotes as $quote) {
            if ($quote->pqProduct->isFlight()) {
                $jobId = \Yii::$app->queue_job->push(new BookingFlightJob($quote->childQuote->getId()));
                \Yii::info([
                    'message' => 'Added job BookingFlightJob',
                    'productQuoteId' => $quote->pq_id,
                    'flightQuoteId' => $quote->childQuote->getId(),
                    'jobId' => $jobId,
                ], 'info\BookingFlightListener');
                return;
            }
        }

        \Yii::error([
            'message' => 'Not found Flight type Quote',
            'orderId' => $event->orderId,
        ], 'BookingFlightListener');
    }
}
