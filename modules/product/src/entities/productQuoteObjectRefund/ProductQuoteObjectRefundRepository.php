<?php

namespace modules\product\src\entities\productQuoteObjectRefund;

class ProductQuoteObjectRefundRepository
{
    public function save(ProductQuoteObjectRefund $objectRefund): int
    {
        if (!$objectRefund->save()) {
            throw new \RuntimeException($objectRefund->getErrorSummary(true)[0]);
        }
        return $objectRefund->pqor_id;
    }
}
