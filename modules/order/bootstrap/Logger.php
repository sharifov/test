<?php

namespace modules\order\bootstrap;

use modules\order\src\entities\order\Order;
use modules\order\src\events\OrderUpdateEvent;
use modules\order\src\services\OrderEntityAttributeFormatterService;
use sales\dispatchers\EventDispatcher;
use sales\helpers\setting\SettingHelper;
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
        $eventDispatcher = \Yii::createObject(EventDispatcher::class);


        if (SettingHelper::isWebhookOrderUpdateBOEnabled()) {
            Event::on(Order::class, ActiveRecord::EVENT_AFTER_UPDATE, static function (AfterSaveEvent $event) use ($eventDispatcher) {

                $oldAttr = self::getOldAttrs($event);
                $newAttr = self::getNewAttrs($event);

                if (empty($oldAttr) && empty($newAttr)) {
                    return;
                }

                $className = get_class($event->sender);
                $globalLogFormatAttrService = \Yii::createObject(OrderEntityAttributeFormatterService::class);
                if ($globalLogFormatAttrService->formatAttr($className, $oldAttr, $newAttr)) {
                    $eventDispatcher->dispatch(new OrderUpdateEvent($event->sender->or_id), Order::UPDATE_EVENT_KEY);
                }
            });
        }
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
