<?php

namespace modules\invoice\src\grid\columns;

use modules\invoice\src\entities\invoice\InvoiceStatus;
use yii\grid\DataColumn;

/**
 * Class InvoiceStatusColumn
 *
 * Ex.
        [
            'class' => \modules\invoice\src\grid\columns\InvoiceStatusColumn::class,
            'attribute' => 'status_id',
        ],
 */
class InvoiceStatusColumn extends DataColumn
{
    public $format = 'invoiceStatus';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = InvoiceStatus::getList();
        }
    }
}
