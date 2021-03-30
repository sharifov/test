<?php

namespace modules\order\src\processManager;

use modules\order\src\entities\order\OrderSourceType;
use modules\order\src\processManager\phoneToBook\OrderProcessManager as PhoneToBookProcessManager;
use modules\order\src\processManager\clickToBook\OrderProcessManager as ClickToBookProcessManager;
use modules\order\src\processManager\phoneToBook\OrderProcessManagerRepository as PhoneToBookRepository;
use modules\order\src\processManager\clickToBook\OrderProcessManagerRepository as ClickToBookRepository;

/**
 * Class OrderProcessManagerFactory
 *
 * @property PhoneToBookRepository $phoneToBookRepository
 * @property ClickToBookRepository $clickToBookRepository
 */
class OrderProcessManagerFactory
{
    private PhoneToBookRepository $phoneToBookRepository;
    private ClickToBookRepository $clickToBookRepository;

    public function __construct(
        PhoneToBookRepository $phoneToBookRepository,
        ClickToBookRepository $clickToBookRepository
    ) {
        $this->phoneToBookRepository = $phoneToBookRepository;
        $this->clickToBookRepository = $clickToBookRepository;
    }

    public function create(int $orderId, $type): void
    {
        $exist = OrderProcessManager::find()->byId($orderId)->exists();

        if ($exist) {
            throw new \DomainException('OrderProcessManager is already exist.');
        }

        if ($type === OrderSourceType::P2B) {
            $manager = PhoneToBookProcessManager::create($orderId, new \DateTimeImmutable());
            $this->phoneToBookRepository->save($manager);
            return;
        }

        if ($type === OrderSourceType::C2B) {
            $manager = ClickToBookProcessManager::create($orderId, new \DateTimeImmutable());
            $this->clickToBookRepository->save($manager);
            return;
        }

        throw new \DomainException('Undefined Type: ' . $type);
    }
}
