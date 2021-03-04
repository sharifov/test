<?php

namespace modules\order\src\listeners\lead;

use common\models\Notifications;
use modules\order\src\entities\order\Order;
use modules\order\src\processManager\events\Orderable;

class OrderProcessStatusReloadLeadOrdersListener
{
    public function handle(Orderable $event): void
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
            \Yii::error($e->getMessage(), 'OrderProcessStatusReloadLeadOrdersListener');
        }
    }
}
