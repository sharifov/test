<?php

namespace modules\order\src\processManager\jobs;

use modules\order\src\processManager\OrderProcessManager;
use modules\order\src\processManager\OrderProcessManagerRepository;
use modules\product\src\entities\productQuote\ProductQuote;
use yii\queue\RetryableJobInterface;

/**
 * Class AfterBookedFlightJob
 *
 * @property int $quoteId
 */
class AfterBookedFlightJob implements RetryableJobInterface
{
    public $quoteId;

    public function __construct(int $quoteId)
    {
        $this->quoteId = $quoteId;
    }

    public function execute($queue): void
    {
        $quote = ProductQuote::findOne($this->quoteId);

        if (!$quote) {
            \Yii::error([
                'message' => 'Not found Quote',
                'quoteId' => $this->quoteId,
            ], 'OrderProcessManager:AfterBookedFlightJob');
            return;
        }

        if (!$quote->pq_order_id) {
            \Yii::error([
                'message' => 'Quote has not relation with Order',
                'quoteId' => $this->quoteId,
            ], 'OrderProcessManager:AfterBookedFlightJob');
            return;
        }

        $process = OrderProcessManager::findOne($quote->pq_order_id);
        if (!$process) {
            \Yii::error([
                'message' => 'Not found Order Process Manager',
                'orderId' => $quote->pq_order_id,
            ], 'OrderProcessManager:AfterBookedFlightJob');
            return;
        }

        if (!$process->isBookingFlight()) {
            \Yii::error([
                'message' => 'Order Process Manager is not in Booking Flight. Status Id: ' . $process->opm_status,
                'orderId' => $quote->pq_order_id,
            ], 'OrderProcessManager:AfterBookedFlightJob');
            return;
        }

        $order = $quote->pqOrder;

        if (!$order) {
            \Yii::error([
                'message' => 'Not found Order',
                'quoteId' => $quote->pq_id,
                'orderId' => $quote->pq_order_id,
            ], 'OrderProcessManager:AfterBookedFlightJob');
            return;
        }

        $quotes = $order->productQuotes;

        if (!$quotes) {
            \Yii::error([
                'message' => 'Not found Quotes for Order',
                'quoteId' => $this->quoteId,
                'orderId' => $order->or_id,
            ], 'OrderProcessManager:AfterBookedFlightJob');
            return;
        }

        $hasDifferentFlightQuote = false;
        foreach ($quotes as $quote) {
            if ($quote->pqProduct->isHotel()) {
                $hasDifferentFlightQuote = true;
                break;
            }
        }

        try {
            $repo = \Yii::createObject(OrderProcessManagerRepository::class);
            if ($hasDifferentFlightQuote) {
                $process->bookingOtherProducts(new \DateTimeImmutable());
            } else {
                $process->booked(new \DateTimeImmutable());
            }
            $repo->save($process);
        } catch (\Throwable $e) {
            throw new \DomainException($e->getMessage() . ' QuoteId: ' . $this->quoteId . ' OrderId: ' . $order->or_id);
        }
    }

    public function getTtr(): int
    {
        return 5;
    }

    public function canRetry($attempt, $error): bool
    {
        \Yii::error([
            'attempt' => $attempt,
            'message' => 'Order process manager save error.',
            'error' => $error->getMessage(),
        ], 'OrderProcessManager:AfterBookedFlightJob');
        return !($attempt > 5);
    }
}
