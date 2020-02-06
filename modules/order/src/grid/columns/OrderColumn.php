<?php

namespace modules\order\src\grid\columns;

use yii\grid\DataColumn;

/**
 * Class OrderColumn
 *
 * @property $relation
 *
 * Ex.
        [
            'class' => \modules\order\src\grid\columns\OrderColumn::class,
            'attribute' => 'osl_order_id',
            'relation' => 'order',
        ],
 */
class OrderColumn extends DataColumn
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
            return $this->grid->formatter->format($model->{$this->relation}, 'order');
        }
        return $this->grid->formatter->format(null, $this->format);
    }
}
