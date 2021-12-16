<?php

namespace modules\product\src\entities\productQuoteObjectRefund\service;

use modules\product\src\entities\productQuoteObjectRefund\QuoteObjectRefundServiceClasses;
use modules\product\src\interfaces\ProductQuoteObjectRefundStructure;

/**
 * Class QuoteObjectRefundManageService
 * @package modules\product\src\entities\productQuoteObjectRefund\service
 *
 * @property-read ProductQuoteObjectRefundStructure $quoteObjectRefundStructure
 */
class QuoteObjectRefundManageService
{
    public function getQuoteObjectRefundStructure(int $productTypeId, int $quoteObjectId): ProductQuoteObjectRefundStructure
    {
        $finder = [QuoteObjectRefundServiceClasses::getClass($productTypeId), 'getRefundStructureObject'];
        $this->quoteObjectRefundStructure = $finder($quoteObjectId);
        return $this->quoteObjectRefundStructure;
    }
}
