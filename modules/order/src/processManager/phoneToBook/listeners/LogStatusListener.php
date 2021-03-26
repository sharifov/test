<?php

namespace modules\order\src\processManager\phoneToBook\listeners;

use modules\order\src\processManager\phoneToBook\events\StatusChangable;

class LogStatusListener
{
    public function handle(StatusChangable $event): void
    {
        \Yii::info([
            'message' => 'Order Process Manager transfer to ' . $event->getStatusName(),
            'orderId' => $event->getOrderId(),
            'date' => $event->getDate(),
        ], 'info\OrderProcessManager::LogStatusListener');
    }
}
