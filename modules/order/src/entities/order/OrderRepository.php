<?php

namespace modules\order\src\entities\order;

use modules\order\src\exceptions\OrderCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class OrderRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class OrderRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): Order
    {
        if ($order = Order::findOne($id)) {
            return $order;
        }
        throw new NotFoundException('Order is not found', OrderCodeException::ORDER_NOT_FOUND);
    }

    public function save(Order $order): int
    {
        if (!$order->save(false)) {
            throw new \RuntimeException('Saving error', OrderCodeException::ORDER_SAVE);
        }
        $this->eventDispatcher->dispatchAll($order->releaseEvents());
        return $order->or_id;
    }

    public function remove(Order $order): void
    {
        if (!$order->delete()) {
            throw new \RuntimeException('Removing error', OrderCodeException::ORDER_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($order->releaseEvents());
    }
}
