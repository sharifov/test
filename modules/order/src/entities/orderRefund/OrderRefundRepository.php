<?php

namespace modules\order\src\entities\orderRefund;

use sales\repositories\NotFoundException;

class OrderRefundRepository
{
    public function find(int $id): OrderRefund
    {
        $refund = OrderRefund::find()->andWhere(['orr_id' => $id])->one();
        if ($refund) {
            return $refund;
        }
        throw new NotFoundException('Order Refund not found. ID: ' . $id);
    }

    public function save(OrderRefund $refund): void
    {
        if (!$refund->save()) {
            throw new \RuntimeException('Order Refund save failed');
        }
    }
}
