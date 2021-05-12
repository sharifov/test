<?php

namespace modules\product\src\entities\productQuoteLead\repository;

use modules\product\src\entities\productQuoteLead\ProductQuoteLead;

class ProductQuoteLeadRepository
{
    public function save(ProductQuoteLead $model)
    {
        if (!$model->save()) {
            throw new \RuntimeException($model->getErrorSummary(true)[0]);
        }
        return $model->getPrimaryKey();
    }
}
