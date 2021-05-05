<?php

namespace modules\order\src\processManager\clickToBook\commands\checkFlight;

use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository;
use modules\order\src\processManager\clickToBook\ErrorOrder;
use modules\order\src\processManager\FlightChecker;
use modules\order\src\processManager\Status;

/**
 * Class Handler
 *
 * @property OrderProcessManagerRepository $repository
 * @property FlightChecker $flightChecker
 * @property ErrorOrder $errorOrder
 */
class Handler
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

    public function handle(Command $command): void
    {
        $manager = $this->repository->find($command->orderId);
        if (!$manager->isNew()) {
            throw new \DomainException('ClickToBook Order Process Manager (Id: ' . $command->orderId . ') must be in New status. Current status: ' . Status::getName($manager->opm_status));
        }
        if (!$this->flightChecker->has($manager->opm_id)) {
            $this->errorOrder->error($manager->opm_id, 'ClickToBook AutoProcessing Error. Not found Flight Product.');
            return;
        }
        $manager->waitBoResponse(new \DateTimeImmutable());
        $this->repository->save($manager);
    }
}
