<?php

namespace modules\shiftSchedule\bootstrap;

use modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule;
use modules\shiftSchedule\src\entities\userShiftScheduleLog\UserShiftScheduleLog;
use modules\shiftSchedule\src\services\UserShiftScheduleAttributeFormatService;
use modules\shiftSchedule\src\services\UserShiftScheduleLogService;
use src\helpers\app\AppHelper;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;

class Logger implements BootstrapInterface
{
    /**
     * @inheritDoc
     */
    public function bootstrap($app): void
    {
        $function = static function (AfterSaveEvent $event) {
            $oldAttr = self::getOldAttrs($event);
            $newAttr = self::getNewAttrs($event);

            if (empty($oldAttr) && empty($newAttr)) {
                return;
            }

            $className = get_class($event->sender);
            $pkName = $event->sender::primaryKey()[0];
            $formatAttributeService = \Yii::createObject(UserShiftScheduleAttributeFormatService::class);
            $formattedAttributes = $formatAttributeService->formatAttr($className, $oldAttr, $newAttr);
            $service = \Yii::createObject(UserShiftScheduleLogService::class);
            try {
                $service->log(
                    $event->sender->attributes[$pkName],
                    UserShiftScheduleLog::getTypeIdByAr($event->name),
                    $oldAttr,
                    $newAttr,
                    $formattedAttributes,
                    \Yii::$app->user->id ?? null
                );
            } catch (\Throwable $e) {
                \Yii::error(AppHelper::throwableLog($e, true), 'ShiftSchedule::Logger');
            }
        };

        Event::on(UserShiftSchedule::class, ActiveRecord::EVENT_AFTER_INSERT, $function);
        Event::on(UserShiftSchedule::class, ActiveRecord::EVENT_AFTER_UPDATE, $function);
    }

    /**
     * @param AfterSaveEvent $event
     * @return string
     */
    private static function getNewAttrs(AfterSaveEvent $event): string
    {
        $newAttr = [];
        foreach ($event->changedAttributes as $key => $attribute) {
            if (array_key_exists($key, $event->sender->attributes)) {
                $newAttr[$key] = $event->sender->attributes[$key];
            }
        }
        return $newAttr ? json_encode($newAttr) : '';
    }

    /**
     * @param AfterSaveEvent $event
     * @return string|null
     */
    private static function getOldAttrs(AfterSaveEvent $event): ?string
    {
        if ($event->name === ActiveRecord::EVENT_AFTER_INSERT) {
            $oldAttr = null;
        } else {
            $oldAttr = $event->changedAttributes ? json_encode($event->changedAttributes) : '';
        }
        return $oldAttr;
    }
}
