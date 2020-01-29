<?php

namespace modules\invoice\src\grid\columns;

use yii\grid\DataColumn;

/**
 * Class InvoiceColumn
 *
 * @property $relation
 *
 * Ex.
        [
            'class' => \modules\invoice\src\grid\columns\InvoiceColumn::class,
            'attribute' => 'prefix_invoice_id',
            'relation' => 'invoice',
        ],
 */
class InvoiceColumn extends DataColumn
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
            return $this->grid->formatter->format($model->{$this->relation}, 'invoice');
        }
        return $this->grid->formatter->format(null, $this->format);
    }
}
