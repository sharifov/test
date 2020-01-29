<?php

namespace modules\product\src\grid\columns;

use modules\product\src\entities\productQuoteOption\ProductQuoteOptionStatus;
use yii\grid\DataColumn;

/**
 * Class ProductQuoteOptionStatusColumn
 *
 * Ex.
        [
            'class' => \modules\product\src\grid\columns\ProductQuoteOptionStatusColumn::class,
            'attribute' => 'pqo_status_id',
        ],
 */
class ProductQuoteOptionStatusColumn extends DataColumn
{
    public $format = 'productQuoteOptionStatus';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = ProductQuoteOptionStatus::getList();
        }
    }
}
