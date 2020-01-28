<?php

namespace modules\order\src\entities\orderProduct;

use modules\order\src\exceptions\OrderCodeException;
use sales\dispatchers\EventDispatcher;
use sales\repositories\NotFoundException;

/**
 * Class OrderProductRepository
 *
 * @property EventDispatcher $eventDispatcher
 */
class OrderProductRepository
{
    private $eventDispatcher;

    public function __construct(EventDispatcher $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function find(int $id): OrderProduct
    {
        if ($orderProduct = OrderProduct::findOne($id)) {
            return $orderProduct;
        }
        throw new NotFoundException('Order product is not found', OrderCodeException::ORDER_PRODUCT_NOT_FOUND);
    }

    public function save(OrderProduct $orderProduct): int
    {
        if (!$orderProduct->save(false)) {
            throw new \RuntimeException('Saving error', OrderCodeException::ORDER_PRODUCT_SAVE);
        }
        $this->eventDispatcher->dispatchAll($orderProduct->releaseEvents());
        return $orderProduct->orp_order_id;
    }

    public function remove(OrderProduct $order): void
    {
        if (!$order->delete()) {
            throw new \RuntimeException('Removing error', OrderCodeException::ORDER_PRODUCT_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($order->releaseEvents());
    }
}
