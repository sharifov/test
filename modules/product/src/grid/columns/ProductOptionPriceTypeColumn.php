<?php

namespace modules\product\src\grid\columns;

use modules\product\src\entities\productOption\ProductOptionPriceType;
use yii\grid\DataColumn;

/**
 * Class ProductOptionPriceTypeColumn
 *
 * Ex.
        [
            'class' => \modules\product\src\grid\columns\ProductOptionPriceTypeColumn::class,
            'attribute' => 'po_price_type_id',
        ],
 */
class ProductOptionPriceTypeColumn extends DataColumn
{
    private const LABEL = 'Price Type';

    public $format = 'productOptionPriceType';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = ProductOptionPriceType::getList();
        }

        if ($this->label === null) {
            $this->label = self::LABEL;
        }
    }
}
