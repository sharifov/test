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

    public function find(int $orderId, int $productQuoteId): OrderProduct
    {
        if ($orderProduct = OrderProduct::findOne(['orp_order_id' => $orderId, 'orp_product_quote_id' => $productQuoteId])) {
            return $orderProduct;
        }
        throw new NotFoundException('Order product is not found', OrderCodeException::ORDER_PRODUCT_NOT_FOUND);
    }

    public function save(OrderProduct $orderProduct): void
    {
        if (!$orderProduct->save(false)) {
            throw new \RuntimeException('Saving error', OrderCodeException::ORDER_PRODUCT_SAVE);
        }
        $this->eventDispatcher->dispatchAll($orderProduct->releaseEvents());
    }

    public function remove(OrderProduct $order): void
    {
        if (!$order->delete()) {
            throw new \RuntimeException('Removing error', OrderCodeException::ORDER_PRODUCT_REMOVE);
        }
        $this->eventDispatcher->dispatchAll($order->releaseEvents());
    }
}
