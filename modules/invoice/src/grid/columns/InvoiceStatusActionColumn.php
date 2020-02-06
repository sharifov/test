<?php

namespace modules\invoice\src\grid\columns;

use modules\invoice\src\entities\invoice\InvoiceStatusAction;
use yii\grid\DataColumn;

/**
 * Class InvoiceStatusActionColumn
 *
 * Ex.
        [
            'class' => \modules\invoice\src\grid\columns\InvoiceStatusActionColumn::class,
            'attribute' => 'prefix_action_id'
        ],
 */
class InvoiceStatusActionColumn extends DataColumn
{
    public $format = 'invoiceStatusAction';

    public function init(): void
    {
        parent::init();

        if ($this->filter === null) {
            $this->filter = InvoiceStatusAction::getList();
        }
    }
}
