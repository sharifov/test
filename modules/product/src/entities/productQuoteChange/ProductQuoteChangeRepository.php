<?php

namespace modules\product\src\entities\productQuoteChange;

use sales\repositories\NotFoundException;

class ProductQuoteChangeRepository
{
    public function findByProductQuoteId(int $id): ProductQuoteChange
    {
        if ($productQuote = ProductQuoteChange::find()->byProductQuote($id)->one()) {
            return $productQuote;
        }
        throw new NotFoundException('Product Quote Change is not found.');
    }

    public function save(ProductQuoteChange $change): void
    {
        if (!$change->save(false)) {
            throw new \RuntimeException('Saving error.');
        }
    }
}
