<?php

namespace modules\product\src\grid\columns;

use modules\product\src\entities\productQuote\ProductQuoteStatus;
use yii\grid\DataColumn;

/**
 * Class ProductQuoteStatusColumn
 *
 * Ex.
        [
            'class' => \modules\product\src\grid\columns\ProductQuoteStatusColumn::class,
            'attribute' => 'pqsl_start_status_id',
        ],
 */
class ProductQuoteStatusColumn extends DataColumn
{
    public $format = 'productQuoteStatus';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = ProductQuoteStatus::getList();
        }
    }
}
