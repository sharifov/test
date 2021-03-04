<?php

namespace modules\order\src\listeners\lead;

use common\models\Notifications;
use modules\order\src\entities\order\events\OrderChangeStatusInterface;
use modules\order\src\entities\order\Order;

class LeadStatusReloadOrdersListener
{
    public function handle(OrderChangeStatusInterface $event): void
    {
        $order = Order::findOne($event->getId());

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
            \Yii::error($e->getMessage(), 'LeadStatusReloadOrdersListener');
        }
    }
}
