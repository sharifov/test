<?php

namespace modules\invoice\src\entities\invoiceStatusLog;

use modules\invoice\src\exceptions\InvoiceCodeException;

/**
 * Class InvoiceStatusLogRepository
 */
class InvoiceStatusLogRepository
{
    public function getPrevious(int $offerId): ?InvoiceStatusLog
    {
        if ($log = InvoiceStatusLog::find()->last($offerId)->one()) {
            return $log;
        }
        return null;
    }

    public function save(InvoiceStatusLog $log): int
    {
        if (!$log->save(false)) {
            throw new \RuntimeException('Saving error', InvoiceCodeException::INVOICE_STATUS_LOG_SAVE);
        }
        return $log->invsl_id;
    }

    public function remove(InvoiceStatusLog $log): void
    {
        if (!$log->delete()) {
            throw new \RuntimeException('Removing error', InvoiceCodeException::INVOICE_STATUS_LOG_REMOVE);
        }
    }
}
