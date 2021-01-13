<?php

namespace modules\order\src\entities\orderTips;

class OrderTipsRepository
{
    public function save(OrderTips $orderTips): OrderTips
    {
        if (!$orderTips->save()) {
            throw new \RuntimeException('Order tips saving error');
        }
        return $orderTips;
    }
}
