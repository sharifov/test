<?php

namespace modules\product\src\grid\columns;

use modules\product\src\entities\productQuote\ProductQuoteStatus;
use yii\grid\DataColumn;

class ProductQuoteStatusColumn extends DataColumn
{
    public $format = 'productQuoteStatus';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = ProductQuoteStatus::STATUS_LIST;
        }
    }
}
