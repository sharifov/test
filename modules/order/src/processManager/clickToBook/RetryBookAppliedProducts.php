<?php

namespace modules\order\src\processManager\clickToBook;

use modules\order\src\entities\order\OrderRepository;
use modules\order\src\processManager\clickToBook\jobs\BookingHotelJob;
use modules\order\src\processManager\jobs\BookingFlightJob;
use modules\order\src\processManager\OrderProcessManagerFactory;
use modules\order\src\processManager\phoneToBook\OrderProcessManager as OrderProcessManagerPhoneToBook;
use modules\order\src\processManager\clickToBook\OrderProcessManager as OrderProcessManagerClickToBook;
use modules\order\src\processManager\queue\Queue;
use modules\product\src\entities\productQuote\ProductQuote;
use src\repositories\NotFoundException;

/**
 * Class RetryBookAppliedProducts
 * @package modules\order\src\processManager\clickToBook
 *
 * @property-read Queue $queue
 * @property-read OrderRepository $orderRepository
 * @property-read OrderProcessManagerRepository $managerRepository
 * @property-read OrderProcessManagerFactory $orderProcessManagerFactory
 */
class RetryBookAppliedProducts
{
    private Queue $queue;
    private OrderRepository $orderRepository;
    private OrderProcessManagerRepository $managerRepository;
    private OrderProcessManagerFactory $orderProcessManagerFactory;

    /**
     * RetryBookAppliedProducts constructor.
     * @param Queue $queue
     * @param OrderProcessManagerRepository $managerRepository
     * @param OrderProcessManagerFactory $orderProcessManagerFactory
     * @param OrderRepository $orderRepository
     */
    public function __construct(
        Queue $queue,
        OrderProcessManagerRepository $managerRepository,
        OrderProcessManagerFactory $orderProcessManagerFactory,
        OrderRepository $orderRepository
    ) {
        $this->queue = $queue;
        $this->orderRepository = $orderRepository;
        $this->managerRepository = $managerRepository;
        $this->orderProcessManagerFactory = $orderProcessManagerFactory;
    }

    /**
     * @param int $orderId
     */
    public function run(int $orderId): void
    {
        $order = $this->orderRepository->find($orderId);

        try {
            /** @var OrderProcessManagerClickToBook */
            $manager = $this->managerRepository->find($order->or_id);

            if (!$manager->isRetrying()) {
                throw new \DomainException('Cannot retry process manager because order process manager is not stopped');
            }
        } catch (NotFoundException $e) {
            $this->orderProcessManagerFactory->create($order->or_id, $order->or_type_id);
            return;
        }

        $flightQuotes = ProductQuote::find()->byOrderId($orderId)->flightQuotes()->applied()->all();

        if ($flightQuotes) {
            foreach ($flightQuotes as $flightQuote) {
                $this->queue->push(new BookingFlightJob($flightQuote->pq_id));
            }
            $manager->waitBoResponse(new \DateTimeImmutable());
            return;
        }

        $quotes = ProductQuote::find()->byOrderId($orderId)->exceptFlightQuotes()->applied()->all();

        if (!$quotes) {
            throw new \DomainException('Not found applied quotes. OrderId: ' . $orderId);
        }

        foreach ($quotes as $quote) {
            if ($quote->pqProduct->isHotel()) {
                $this->queue->push(new BookingHotelJob($quote->childQuote->getId()));
            }
        }
    }
}
