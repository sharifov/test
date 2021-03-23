<?php

namespace modules\product\src\entities\productOption\serializer;

use modules\product\src\entities\productOption\ProductOption;
use sales\entities\serializer\Serializer;

/**
 * Class ProductOptionSerializer
 * @package modules\product\src\entities\productOption\serializer
 *
 * @property ProductOption $model
 */
class ProductOptionSerializer extends Serializer
{
    public function __construct(ProductOption $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritDoc
     */
    public static function fields(): array
    {
        return [
            'po_key',
            'po_name',
            'po_description'
        ];
    }

    /**
     * @inheritDoc
     */
    public function getData(): array
    {
        return $this->toArray();
    }
}
