<?php

namespace modules\order\src\processManager\listeners;

use modules\order\src\entities\order\events\OrderPaymentPaidEvent;
use modules\order\src\jobs\OrderCompleteJob;
use modules\order\src\processManager\OrderProcessManager;

class OrderProcessOrderCompleteListener
{
    public function handle(OrderPaymentPaidEvent $event): void
    {
        $process = OrderProcessManager::findOne($event->orderId);

        if (!$process) {
            return;
        }

        if (!$process->isBooked()) {
            return;
        }

        \Yii::$app->queue_job->push(new OrderCompleteJob($event->orderId));
    }
}
