<?php

namespace modules\order\src\entities\orderData;

class OrderDataRepository
{
    public function save(OrderData $orderData): int
    {
        if ($orderData->save()) {
            return $orderData->od_order_id;
        }
        throw new \RuntimeException('Order data saving failed');
    }
}
