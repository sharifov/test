<?php

namespace modules\product\src\entities\productHolder;

class ProductHolderRepository
{
    public function save(ProductHolder $holder): int
    {
        if ($holder->save()) {
            return $holder->ph_id;
        }
        throw new \RuntimeException('Product holder saving failed: ' . $holder->getErrorSummary(true)[0]);
    }
}
