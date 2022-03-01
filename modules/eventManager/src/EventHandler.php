<?php

namespace modules\eventManager\src;

use yii\base\Event;
use yii\helpers\VarDumper;

class EventHandler
{
    /**
     * @param Event $event
     * @return void
     */
    public static function handler(Event $event): void
    {

        $eventName = $event->name;
        $eventSender = $event->sender;

        VarDumper::dump($event, 10, true);
        exit;
        $data = $params->data;
        \Yii::info(['data' => $data], 'info\EventHandler');
    }
}
