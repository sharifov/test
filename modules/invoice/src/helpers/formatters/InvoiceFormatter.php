<?php

namespace modules\invoice\src\helpers\formatters;

use modules\invoice\src\entities\invoice\Invoice;
use yii\bootstrap4\Html;

class InvoiceFormatter
{
    public static function asInvoice(Invoice $invoice): string
    {
        return Html::a(
            'invoice: ' . $invoice->inv_id,
            ['/invoice/invoice-crud/view', 'id' => $invoice->inv_id],
            ['target' => '_blank', 'data-pjax' => 0]
        );
    }
}
