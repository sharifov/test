<?php

namespace modules\order\src\processManager;

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

    public function find(int $id): OrderProcessManager
    {
        if ($process = OrderProcessManager::findOne($id)) {
            return $process;
        }
        throw new NotFoundException('Order Process Manager is not found');
    }

    public function save(OrderProcessManager $process): void
    {
        if (!$process->save(false)) {
            throw new \RuntimeException('Saving error');
        }
        $this->eventDispatcher->dispatchAll($process->releaseEvents());
    }

    public function remove(OrderProcessManager $process): void
    {
        if (!$process->delete()) {
            throw new \RuntimeException('Removing error');
        }
        $this->eventDispatcher->dispatchAll($process->releaseEvents());
    }
}
