<?php

namespace modules\order\src\payment\listeners;

use common\models\Notifications;
use common\models\Payment;
use modules\order\src\payment\events\Paymentable;

class PaymentStatusReloadLeadOrdersListener
{
    public function handle(Paymentable $event): void
    {
        $payment = Payment::findOne($event->getPaymentId());

        if (!$payment) {
            return;
        }

        $order = $payment->payOrder;

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
            \Yii::error($e->getMessage(), 'PaymentStatusReloadLeadOrdersListener');
        }
    }
}
