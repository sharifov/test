<?php

namespace modules\invoice\src\entities\invoiceStatusLog;

use yii\db\ActiveQuery;

/**
 * @see InvoiceStatusLog
 */
class Scopes extends ActiveQuery
{
    public function last(int $invoiceId): self
    {
        return $this
            ->andWhere(['invsl_invoice_id' => $invoiceId])
            ->orderBy(['invsl_id' => SORT_DESC])
            ->limit(1);
    }
}
