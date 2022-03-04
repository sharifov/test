<?php

namespace modules\eventManager\src;

use src\helpers\app\AppHelper;
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
        $params = $event->data;

        $eventList = Yii::$app->event->getEventListByKey($eventName);
        if (!empty($eventList)) {
            //VarDumper::dump($eventList, 10, true);            exit;

            foreach ($eventList as $eventItem) {
                if (!empty($eventItem['el_enable_log'])) {
                    $infoData = [];
                    $infoData['name'] = $eventName;
                    $infoData['event'] = ['name' => $eventName, 'params' => $params];
                    $infoData['event-item-db'] = $eventItem;
                    \Yii::info($infoData, 'info\EventApp:event-' . $eventItem['el_id']);
                }

                if (!empty($eventItem['handlerList'])) {
                    foreach ($eventItem['handlerList'] as $handlerItem) {
                        if (!empty($handlerItem['eh_enable_log'])) {
                            $infoData = [];
                            $infoData['name'] = $eventName;
                            $infoData['event'] = ['name' => $eventName, 'params' => $params];
                            $infoData['event-item-db'] = $eventItem;
                            $infoData['handler-item'] = $handlerItem;
                            \Yii::info($infoData, 'info\EventApp:handler-' . $handlerItem['eh_id']);
                        }

                        try {
                            $obj = Yii::createObject($handlerItem['eh_class']);
                            $method = $handlerItem['eh_method'];
                            echo $obj->$method($params);
                        } catch (\Throwable $throwable) {
                            $infoData = AppHelper::throwableLog($throwable);
                            $infoData['event'] = ['name' => $eventName, 'params' => $params];
                            $infoData['event-item-db'] = $eventItem;
                            $infoData['handler-item'] = $handlerItem;
                            Yii::error($infoData, 'EventApp:handler:throwable');
                        }
                    }
                }
            }
        }
    }
}
