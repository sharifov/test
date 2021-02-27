<?php

namespace modules\order\src\processManager\listeners;

use modules\order\src\processManager\events\Orderable;

class LogCreatedListener
{
    public function handle(Orderable $event): void
    {
        \Yii::info([
            'message' => 'Order Process Manager created',
            'orderId' => $event->getOrderId(),
            'date' => $event->getDate(),
        ], 'info\OrderProcessManager::LogCreatedListener');
    }
}
