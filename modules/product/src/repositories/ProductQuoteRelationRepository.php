<?php

namespace modules\product\src\repositories;

use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\exceptions\ProductCodeException;

/**
 * Class ProductQuoteRelationRepository
 */
class ProductQuoteRelationRepository
{
    public function save(ProductQuoteRelation $model): void
    {
        if (!$model->save(false)) {
            throw new \RuntimeException('Saving error', ProductCodeException::PRODUCT_QUOTE_RELATION_SAVE);
        }
    }
}
