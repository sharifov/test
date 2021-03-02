<?php

namespace modules\invoice\src\jobs;

use modules\invoice\src\entities\invoice\Invoice;
use modules\invoice\src\entities\invoice\InvoiceRepository;
use yii\queue\RetryableJobInterface;

/**
 * Class PaidInvoiceJob
 *
 * @property int $invoiceId
 */
class PaidInvoiceJob implements RetryableJobInterface
{
    public $invoiceId;

    public function __construct(int $invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    public function execute($queue)
    {
        $invoice = Invoice::findOne($this->invoiceId);

        if (!$invoice) {
            \Yii::error([
                'message' => 'Paid invoice error',
                'error' => 'Not found Invoice',
                'invoiceId' => $this->invoiceId,
            ], 'PaidInvoiceJob');
            return;
        }

        if ($invoice->isPaid()) {
            \Yii::error([
                'message' => 'Paid invoice error',
                'error' => 'Invoice already paid',
                'invoiceId' => $invoice->inv_id,
            ], 'PaidInvoiceJob');
            return;
        }

        $repo = \Yii::createObject(InvoiceRepository::class);
        $invoice->paid();
        $repo->save($invoice);
    }

    public function getTtr(): int
    {
        return 1 * 60;
    }

    public function canRetry($attempt, $error): bool
    {
        \Yii::error([
            'attempt' => $attempt,
            'message' => 'Invoice Paid error',
            'error' => $error->getMessage(),
            'invoiceId' => $this->invoiceId,
        ], 'PaidInvoiceJob');
        return !($attempt > 5);
    }
}
