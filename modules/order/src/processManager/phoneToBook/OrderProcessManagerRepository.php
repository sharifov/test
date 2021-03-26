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

    public function get(int $id): ?OrderProcessManager
    {
        if ($process = OrderProcessManager::find()->byId($id)->phoneToBook()->one()) {
            return $process;
        }
        return null;
    }

    public function find(int $id): OrderProcessManager
    {
        if ($process = OrderProcessManager::find()->byId($id)->phoneToBook()->one()) {
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
}
