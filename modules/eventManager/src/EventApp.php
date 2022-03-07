<?php

namespace modules\eventManager\src;

use common\components\jobs\EventAppHandlerJob;
use src\helpers\app\AppHelper;
use Yii;
use yii\base\ErrorException;
use yii\base\Event;
use yii\helpers\Json;
use yii\helpers\VarDumper;

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
        $eventData = $event->data;

        $eventList = Yii::$app->event->getEventListByKey($eventName);
        if (!empty($eventList)) {
            //VarDumper::dump($eventList, 10, true);            exit;

            foreach ($eventList as $eventItem) {
                $enableType = (int) $eventItem['el_enable_type'];
                $isRun = Yii::$app->event::isDue($enableType, $eventItem['el_cron_expression']);

                $eventParams = empty($eventItem['el_params']) ? [] : Json::decode($eventItem['el_params']);

                if ($isRun) {
                    if (!empty($eventItem['el_enable_log'])) {
                        $infoData = [];
                        $infoData['name'] = $eventName;
                        $infoData['event'] = ['name' => $eventName, 'data' => $eventData];
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

                            $handlerParams = empty($handlerItem['eh_params']) ? [] :
                                Json::decode($handlerItem['eh_params']);

//                            VarDumper::dump($handlerParams, 10, true); exit;

                            $infoData = [];
                            if (!empty($handlerItem['eh_enable_log'])) {
                                $infoData['name'] = $eventName;
                                $infoData['event'] = ['name' => $eventName, 'data' => $eventData];
                                $infoData['event-item-db'] = $eventItem;
                                $infoData['handler-item'] = $handlerItem;
                                \Yii::info($infoData, 'info\EventApp:handler-' . $handlerItem['eh_id']);
                            }

                            try {
                                if (class_exists($handlerItem['eh_class'])) {
                                    $obj = Yii::createObject($handlerItem['eh_class']);
                                    $method = $handlerItem['eh_method'];
                                    if (method_exists($obj, $method)) {
                                        if ($handlerItem['eh_asynch']) {
                                            $job = new EventAppHandlerJob($obj, $method, $eventData);
                                            $job->eventParams = $eventParams;
                                            $job->handlerParams = $handlerParams;

                                            $job->enableLog = (bool) $handlerItem['eh_enable_log'];
                                            if ($job->enableLog) {
                                                $job->infoData = $infoData;
                                            }
                                            Yii::$app->queue_job->priority(100)->push($job);
                                        } else {
                                            $obj->$method($eventData, $eventParams, $handlerParams);
                                            if ($handlerItem['eh_break']) {
                                                break;
                                            }
                                        }
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
                                $infoData['event'] = ['name' => $eventName, 'params' => $eventData];
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
