<?php

namespace modules\eventManager\src;

use Yii;
use yii\base\Event;
use yii\helpers\VarDumper;

class EventApp
{
    /**
     * @param Event $event
     * @return void
     */
    public static function handler(Event $event): void
    {

        $eventName = $event->name;
        $eventList = Yii::$app->event->getEventListByKey($eventName);
        if (!empty($eventList)) {
//            VarDumper::dump($eventList, 10, true);
            foreach ($eventList as $eventItem) {
                if (!empty($eventItem['handlerList'])) {
                    foreach ($eventItem['handlerList'] as $handlerItem) {

                        //if ($handlerItem['eh_'])
                        $obj = Yii::createObject($handlerItem['eh_class']);
                        $method = $handlerItem['eh_method'];
                        $obj->$method;
                        //echo $handlerItem['eh_class'] . '::' . $handlerItem['eh_method'];
                    }
                }
            }
        }
        exit;


        // $eventSender = $event->sender;


        VarDumper::dump($eventList, 10, true);
        exit;
        $data = $params->data;
        \Yii::info(['data' => $data], 'info\EventHandler');
    }
}
