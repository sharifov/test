<?php

namespace modules\product\src\entities\productQuoteOrigin\repository;

use modules\product\src\entities\productQuoteOrigin\ProductQuoteOrigin;

class ProductQuoteOriginRepository
{
    public function save(ProductQuoteOrigin $model)
    {
        if (!$model->save()) {
            throw new \RuntimeException($model->getErrorSummary(true)[0]);
        }
        return $model->getPrimaryKey();
    }
}
