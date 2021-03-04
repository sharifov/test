<?php

namespace modules\invoice\src\listeners;

use common\models\Notifications;
use modules\invoice\src\entities\invoice\events\Invoiceable;
use modules\invoice\src\entities\invoice\Invoice;

class InvoiceStatusReloadLeadOrdersListener
{
    public function handle(Invoiceable $event): void
    {
        $invoice = Invoice::findOne($event->getInvoiceId());

        if (!$invoice) {
            return;
        }

        $order = $invoice->invOrder;

        if (!$order) {
            return;
        }

        if (!$order->or_lead_id) {
            return;
        }

        try {
            Notifications::pub(
                ['lead-' . $order->or_lead_id],
                'reloadOrders',
                ['data' => []]
            );
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage(), 'InvoiceStatusReloadLeadOrdersListener');
        }
    }
}
