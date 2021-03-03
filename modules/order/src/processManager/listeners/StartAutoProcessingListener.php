<?php

namespace modules\order\src\processManager\listeners;

use modules\order\src\events\OrderProcessingEvent;
use modules\order\src\processManager\jobs\StartAutoProcessingJob;
use sales\helpers\setting\SettingHelper;

class StartAutoProcessingListener
{
    public function handle(OrderProcessingEvent $event): void
    {
        if (SettingHelper::orderAutoProcessingEnable()) {
            \Yii::$app->queue_job->push(new StartAutoProcessingJob($event->order->or_id));
        }
    }
}
