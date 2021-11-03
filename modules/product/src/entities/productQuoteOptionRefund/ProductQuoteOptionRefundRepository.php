<?php

namespace modules\product\src\entities\productQuoteOptionRefund;

class ProductQuoteOptionRefundRepository
{
    public function save(ProductQuoteOptionRefund $productQuoteOptionRefund): int
    {
        if (!$productQuoteOptionRefund->save()) {
            throw new \RuntimeException('Product Quote Option Refund saving failed: ' . $productQuoteOptionRefund->getErrorSummary(true)[0]);
        }
        return $productQuoteOptionRefund->pqor_id;
    }
}
