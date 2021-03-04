<?php

namespace modules\order\src\listeners\lead;

use common\models\Notifications;

class LeadReloadOrdersListener
{
    public function handle($event): void
    {
        Notifications::pub(
            ['lead-' . $event->chatId],
            'reloadOrders',
            ['data' => []]
        );
    }
}
