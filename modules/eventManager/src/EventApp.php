<?php

namespace modules\eventManager\src;

use src\helpers\app\AppHelper;
use Yii;
use yii\base\ErrorException;
use yii\base\Event;

class EventApp
{
    public const HANDLER = 'handler';

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
                $enableType = (int) $eventItem['el_enable_type'];
                $isRun = Yii::$app->event::isDue($enableType, $eventItem['el_cron_expression']);

                if ($isRun) {
                    if (!empty($eventItem['el_enable_log'])) {
                        $infoData = [];
                        $infoData['name'] = $eventName;
                        $infoData['event'] = ['name' => $eventName, 'params' => $params];
                        $infoData['event-item-db'] = $eventItem;
                        \Yii::info($infoData, 'info\EventApp:event-' . $eventItem['el_id']);
                    }

                    if (!empty($eventItem['handlerList'])) {
                        foreach ($eventItem['handlerList'] as $handlerItem) {
                            if (
                                !Yii::$app->event::isDue(
                                    (int) $handlerItem['eh_enable_type'],
                                    $handlerItem['eh_cron_expression']
                                )
                            ) {
                                continue;
                            }

                            if (!empty($handlerItem['eh_enable_log'])) {
                                $infoData = [];
                                $infoData['name'] = $eventName;
                                $infoData['event'] = ['name' => $eventName, 'params' => $params];
                                $infoData['event-item-db'] = $eventItem;
                                $infoData['handler-item'] = $handlerItem;
                                \Yii::info($infoData, 'info\EventApp:handler-' . $handlerItem['eh_id']);
                            }

                            try {
                                if (class_exists($handlerItem['eh_class'])) {
                                    $obj = Yii::createObject($handlerItem['eh_class']);
                                    $method = $handlerItem['eh_method'];
                                    if (method_exists($obj, $method)) {
                                        $obj->$method($params);
                                    } else {
                                        throw new ErrorException(
                                            'The requested object method does not exist.',
                                            1001
                                        );
                                    }
                                } else {
                                    throw new ErrorException(
                                        'The requested object class does not exist.',
                                        1002
                                    );
                                }
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
}
