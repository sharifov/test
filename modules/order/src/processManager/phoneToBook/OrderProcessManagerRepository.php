<?php

namespace modules\order\src\processManager\phoneToBook;

use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class OrderProcessManagerRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class OrderProcessManagerRepository
{
    private EventDispatcher $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function exist(int $id): bool
    {
        return OrderProcessManager::find()->byId($id)->phoneToBook()->exists();
    }

    public function get(int $id): ?OrderProcessManager
    {
        if ($manager = OrderProcessManager::find()->byId($id)->phoneToBook()->one()) {
            return $manager;
        }
        return null;
    }

    public function find(int $id): OrderProcessManager
    {
        if ($manager = OrderProcessManager::find()->byId($id)->phoneToBook()->one()) {
            return $manager;
        }
        throw new NotFoundException('Order Process Manager is not found');
    }

    public function save(OrderProcessManager $manager): void
    {
        if (!$manager->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($manager->releaseEvents());
    }
}
