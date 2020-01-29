<?php

namespace modules\product\src\grid\columns;

use yii\grid\DataColumn;

/**
 * Class ProductColumn
 *
 * @property $relation
 *
 *  Ex.
        [
            'class' => modules\product\src\grid\columns\ProductColumn::class,
            'attribute' => 'product_id',
            'relation' => 'product',
        ],
 *
 */
class ProductColumn extends DataColumn
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
            return $this->grid->formatter->format($model->{$this->relation}, 'product');
        }
        return $this->grid->formatter->format(null, $this->format);
    }
}
