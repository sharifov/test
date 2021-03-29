<?php

namespace modules\order\src\processManager\listeners;

use modules\order\src\entities\order\Order;
use modules\order\src\events\OrderProcessingEvent;
use modules\order\src\processManager;
use sales\helpers\setting\SettingHelper;

/**
 * Class StartAutoProcessingListener
 *
 * @property processManager\Queue $queue
 */
class StartAutoProcessingListener
{
    private processManager\Queue $queue;

    public function __construct(processManager\Queue $queue)
    {
        $this->queue = $queue;
    }

    public function handle(OrderProcessingEvent $event): void
    {
        $order = Order::findOne($event->getId());

        if (!$order) {
            return;
        }

        if (!SettingHelper::orderAutoProcessingEnable()) {
            return;
        }

        if ($order->isPhoneToBook()) {
            $this->queue->push(new processManager\phoneToBook\jobs\StartAutoProcessingJob($event->order->or_id));
            return;
        }

        if ($order->isClickToBook()) {
            $this->queue->push(new processManager\clickToBook\jobs\CreateOrderProcessManagerJob($event->order->or_id));
            return;
        }
    }
}
