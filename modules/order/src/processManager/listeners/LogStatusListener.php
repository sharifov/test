<?php

namespace modules\order\src\processManager\listeners;

use modules\order\src\processManager\events\StatusChangable;

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
