<?php

namespace modules\order\src\processManager\clickToBook\listeners;

use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\ErrorOrder;
use modules\order\src\processManager\events\CreatedEvent;
use modules\order\src\processManager\FlightChecker;

/**
 * Class CheckFlightListener
 *
 * @property OrderProcessManagerRepository $repository
 * @property FlightChecker $flightChecker
 * @property ErrorOrder $errorOrder
 */
class CheckFlightListener
{
    private OrderProcessManagerRepository $repository;
    private FlightChecker $flightChecker;
    private ErrorOrder $errorOrder;

    public function __construct(
        OrderProcessManagerRepository $repository,
        FlightChecker $flightChecker,
        ErrorOrder $errorOrder
    ) {
        $this->repository = $repository;
        $this->flightChecker = $flightChecker;
        $this->errorOrder = $errorOrder;
    }

    public function handle(CreatedEvent $event): void
    {
        $manager = $this->repository->get($event->getOrderId());
        if (!$manager) {
            return;
        }
        if (!$manager->isNew()) {
            return;
        }
        if (!$this->flightChecker->has($manager->opm_id)) {
            $this->errorOrder->error($manager->opm_id);
            return;
        }
        $manager->waitBoResponse(new \DateTimeImmutable());
        $this->repository->save($manager);
    }
}
