<?php

namespace modules\product\src\grid\columns;

use modules\product\src\entities\productType\ProductTypeQuery;
use yii\grid\DataColumn;

/**
 * Class ProductTypeColumn
 *
 * @property boolean $onlyEnabled
 *
 * Ex.
        [
            'class' => \modules\product\src\grid\columns\ProductTypeColumn::class,
            'attribute' => 'product_type_id',
        ],
 */
class ProductTypeColumn extends DataColumn
{
    private const LABEL = 'Product Type';

    public $format = 'productType';

    public $onlyEnabled = false;

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            if ($this->onlyEnabled) {
                $this->filter = ProductTypeQuery::getListEnabled();
            } else {
                $this->filter = ProductTypeQuery::getListAll();
            }
        }

        if ($this->label === null) {
            $this->label = self::LABEL;
        }
    }
}
