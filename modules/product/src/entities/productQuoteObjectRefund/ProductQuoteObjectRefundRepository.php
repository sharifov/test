<?php

namespace modules\product\src\entities\productQuoteObjectRefund;

use src\repositories\NotFoundException;

class ProductQuoteObjectRefundRepository
{
    public function save(ProductQuoteObjectRefund $objectRefund): int
    {
        if (!$objectRefund->save()) {
            throw new \RuntimeException('Product Quote Object Refund saving failed: ' . $objectRefund->getErrorSummary(true)[0]);
        }
        return $objectRefund->pqor_id;
    }

    public function find(int $id): ProductQuoteObjectRefund
    {
        if ($object = ProductQuoteObjectRefund::findOne(['pqor_id' => $id])) {
            return $object;
        }
        throw new NotFoundException('ProductQuoteObjectRefund not found by id: ' . $id);
    }
}
