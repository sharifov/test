<?php

namespace modules\order\src\listeners\lead;

use common\models\Notifications;
use modules\order\src\entities\order\events\OrderPaymentStatusable;
use modules\order\src\entities\order\Order;

class LeadPaymentStatusReloadOrdersListener
{
    public function handle(OrderPaymentStatusable $event): void
    {
        $order = Order::findOne($event->getOrderId());

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
            \Yii::error($e->getMessage(), 'LeadPaymentStatusReloadOrdersListener');
        }
    }
}
