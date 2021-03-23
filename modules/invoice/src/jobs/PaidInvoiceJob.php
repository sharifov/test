<?php

namespace modules\invoice\src\jobs;

use modules\invoice\src\entities\invoice\Invoice;
use modules\invoice\src\entities\invoice\InvoiceRepository;
use yii\queue\JobInterface;
use yii\queue\RetryableJobInterface;

/**
 * Class PaidInvoiceJob
 *
 * @property int $invoiceId
 */
class PaidInvoiceJob implements JobInterface
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

        try {
            $repo = \Yii::createObject(InvoiceRepository::class);
            $invoice->paid();
            $repo->save($invoice);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Transfer Invoice to job paid error',
                'error' => $e->getMessage(),
                'invoiceId' => $this->invoiceId,
            ], 'PaidInvoiceJob');
        }
    }

//    public function getTtr(): int
//    {
//        return 1 * 60;
//    }
//
//    public function canRetry($attempt, $error): bool
//    {
//        \Yii::error([
//            'attempt' => $attempt,
//            'message' => 'Invoice Paid error',
//            'error' => $error->getMessage(),
//            'invoiceId' => $this->invoiceId,
//        ], 'PaidInvoiceJob');
//        return !($attempt > 5);
//    }
}
