<?php

namespace modules\product\src\entities\productQuoteRefund;

use sales\repositories\NotFoundException;

class ProductQuoteRefundRepository
{
    public function find(int $id): ProductQuoteRefund
    {
        $refund = ProductQuoteRefund::find()->andWhere(['pqr_id' => $id])->one();
        if ($refund) {
            return $refund;
        }
        throw new NotFoundException('Product Quote Refund not found. ID: ' . $id);
    }

    public function save(ProductQuoteRefund $refund): void
    {
        if (!$refund->save()) {
            throw new \RuntimeException('Order Refund save failed');
        }
    }
}
