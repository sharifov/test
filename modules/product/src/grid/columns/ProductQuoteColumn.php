<?php

namespace modules\product\src\grid\columns;

use yii\grid\DataColumn;


/**
 * Class ProductQuoteColumn
 *
 * @property $relation
 *
 *  Ex.
        [
            'class' => modules\product\src\grid\columns\ProductQuoteColumn::class,
            'attribute' => 'product_quote_id',
            'relation' => 'productQuote',
        ],
 *
 */
class ProductQuoteColumn extends DataColumn
{
    public $relation;

    public function init(): void
    {
        parent::init();
        if (!$this->relation) {
            throw new \InvalidArgumentException('relation must be set.');
        }
    }

    protected function renderDataCellContent($model, $key, $index): string
    {
        if ($model->{$this->attribute}) {
            return $this->grid->formatter->format($model->{$this->relation}, 'productQuote');
        }
        return $this->grid->formatter->format(null, $this->format);
    }
}
