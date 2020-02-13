<?php

namespace modules\product\src\grid\columns;

use modules\product\src\entities\productQuote\ProductQuoteStatusAction;
use yii\grid\DataColumn;

/**
 * Class ProductQuoteStatusActionColumn
 *
 * Ex.
        [
            'class' => \modules\product\src\grid\columns\ProductQuoteStatusActionColumn::class,
            'attribute' => 'pqsl_action_id',
        ],
 */
class ProductQuoteStatusActionColumn extends DataColumn
{
    public $format = 'productQuoteStatusAction';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = ProductQuoteStatusAction::getList();
        }
    }
}
