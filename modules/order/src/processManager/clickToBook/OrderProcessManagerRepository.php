<?php

namespace modules\order\src\processManager\clickToBook;

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
        return OrderProcessManager::find()->byId($id)->clickToBook()->exists();
    }

    public function get(int $id): ?OrderProcessManager
    {
        if ($manager = OrderProcessManager::find()->byId($id)->clickToBook()->one()) {
            return $manager;
        }
        return null;
    }

    public function find(int $id): OrderProcessManager
    {
        if ($manager = OrderProcessManager::find()->byId($id)->clickToBook()->one()) {
            return $manager;
        }
        throw new NotFoundException('ClickToBook Order Process Manager is not found. Id: ' . $id);
    }

    public function save(OrderProcessManager $manager): void
    {
        if (!$manager->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($manager->releaseEvents());
    }
}
