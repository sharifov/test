<?php

namespace modules\invoice\src\listeners;

use common\models\Payment;
use modules\invoice\src\jobs\PaidInvoiceJob;
use modules\order\src\payment\events\PaymentCompletedEvent;

class PaidInvoiceListener
{
    public function handle(PaymentCompletedEvent $event): void
    {
        $payment = Payment::findOne($event->paymentId);

        if (!$payment) {
            \Yii::error([
                'message' => 'Paid invoice error',
                'error' => 'Not found payment',
                'paymentId' => $event->paymentId,
            ], 'PaidInvoiceListener');
            return;
        }

        $invoice = $payment->payInvoice;

        if (!$invoice) {
            \Yii::error([
                'message' => 'Paid invoice error',
                'error' => 'Not found relation Invoice',
                'paymentId' => $event->paymentId,
            ], 'PaidInvoiceListener');
            return;
        }

        if ($invoice->isPaid()) {
            \Yii::error([
                'message' => 'Paid invoice error',
                'error' => 'Invoice already paid',
                'paymentId' => $event->paymentId,
                'invoiceId' => $invoice->inv_id,
            ], 'PaidInvoiceListener');
            return;
        }

        \Yii::$app->queue_job->push(new PaidInvoiceJob($invoice->inv_id));
    }
}
