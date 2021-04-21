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

    public function remove(OrderTips $model): void
    {
        if (!$model->delete()) {
            throw new \RuntimeException('OrderTips remove fail');
        }
    }

    public function removeByOrderId(int $orderId): array
    {
        $removedIds = [];
        foreach (OrderTips::findAll(['ot_order_id' => $orderId]) as $model) {
            $id = $model->ot_order_id;
            $this->remove($model);
            $removedIds[] = $id;
        }
        return $removedIds;
    }
}
