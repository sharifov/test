<?php

namespace modules\order\src\processManager\clickToBook\commands\checkOrderIsPayment;

use modules\order\src\processManager\AppliedProductsBookingRunner;
use modules\order\src\processManager\BookableQuoteChecker;
use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\clickToBook\ErrorOrder;
use modules\order\src\processManager\Status;

/**
 * @property OrderProcessManagerRepository $repository
 * @property ErrorOrder $errorOrder
 * @property BookableQuoteChecker $bookableQuoteChecker
 * @property AppliedProductsBookingRunner $bookingRunner
 */
class Handler
{
    private OrderProcessManagerRepository $repository;
    private ErrorOrder $errorOrder;
    private BookableQuoteChecker $bookableQuoteChecker;
    private AppliedProductsBookingRunner $bookingRunner;

    public function __construct(
        OrderProcessManagerRepository $repository,
        ErrorOrder $errorOrder,
        BookableQuoteChecker $bookableQuoteChecker,
        AppliedProductsBookingRunner $bookingRunner
    ) {
        $this->repository = $repository;
        $this->errorOrder = $errorOrder;
        $this->bookableQuoteChecker = $bookableQuoteChecker;
        $this->bookingRunner = $bookingRunner;
    }

    public function handle(Command $command): void
    {
        $manager = $this->repository->find($command->orderId);

        if (!$manager->isFlightProductProcessed()) {
            throw new \DomainException('ClickToBook Order Process Manager (Id: ' . $manager->opm_id . ') is not Flight Product Processed status. Current Status: ' . Status::getName($manager->opm_status));
        }

        $order = $manager->order;

//        if (!$order->isPaymentPaid()) {
//            $this->errorOrder->error($order->or_id, 'ClickToBook AutoProcessing error. Order Payment is not Paid.');
//            return;
//        }

        if ($this->bookableQuoteChecker->has($order->or_id)) {
            $this->bookingRunner->run($order->or_id);
            return;
        }

        $manager->booked(new \DateTimeImmutable());
        $this->repository->save($manager);
    }
}
