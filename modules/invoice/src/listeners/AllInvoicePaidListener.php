<?php

namespace modules\invoice\src\listeners;

use modules\invoice\src\entities\invoice\events\InvoicePaidEvent;
use modules\invoice\src\entities\invoice\Invoice;
use modules\order\src\jobs\OrderPaymentPaidJob;

class AllInvoicePaidListener
{
    public function handle(InvoicePaidEvent $event): void
    {
        if (!$event->orderId) {
            \Yii::error([
                'message' => 'Not found relation order id',
                'error' => 'Not found payment',
                'invoiceId' => $event->invoiceId,
            ], 'AllInvoicePaidListener');
            return;
        }

        $invoices = Invoice::find()->andWhere(['inv_order_id' => $event->orderId])->all();

        if (!$invoices) {
            \Yii::error([
                'message' => 'Not found invoices',
                'orderId' => $event->orderId,
            ], 'AllInvoicePaidListener');
            return;
        }

        /** @var Invoice[] $invoices */
        foreach ($invoices as $invoice) {
            if (!$invoice->isPaid()) {
                return;
            }
        }

        \Yii::$app->queue_job->push(new OrderPaymentPaidJob($event->orderId));
    }
}
