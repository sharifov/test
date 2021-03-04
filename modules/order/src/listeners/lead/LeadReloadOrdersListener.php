<?php

namespace modules\order\src\listeners\lead;

use common\models\Notifications;

class LeadReloadOrdersListener
{
    public function handle($event): void
    {
        try {
            Notifications::pub(
                ['lead-' . $event->chatId],
                'reloadOrders',
                ['data' => []]
            );
        } catch (\Throwable $e) {
            \Yii::error($e->getMessage(), 'LeadReloadOrdersListener');
        }
    }
}
