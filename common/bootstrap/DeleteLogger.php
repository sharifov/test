<?php

namespace common\bootstrap;

use common\models\GlobalLog;
use modules\abac\src\entities\AbacPolicy;
use src\logger\db\GlobalLogInterface;
use src\logger\db\LogDTO;
use src\services\log\GlobalEntityAttributeFormatServiceService;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class DeleteLogger implements BootstrapInterface
{
    private const CLASSES = [
        AbacPolicy::class => AbacPolicy::class
    ];

    /**
     * @param \yii\base\Application $app
     */
    public function bootstrap($app): void
    {
        $funcDelete = static function (Event $event) {
            if (ArrayHelper::keyExists(get_class($event->sender), self::CLASSES)) {
                $globalLogFormatAttrService = \Yii::createObject(GlobalEntityAttributeFormatServiceService::class);
                $attributes = json_encode($event->sender->attributes);

                $log = \Yii::createObject(GlobalLogInterface::class);
                $pkName = $event->sender::primaryKey()[0];

                $model = get_class($event->sender);
                $log->log(
                    new LogDTO(
                        get_class($event->sender),
                        $event->sender->attributes[$pkName],
                        \Yii::$app->id,
                        \Yii::$app->user->id ?? null,
                        $attributes,
                        $attributes,
                        $globalLogFormatAttrService->formatAttr($model, $attributes, $attributes),
                        GlobalLog::ACTION_TYPE_DELETE
                    )
                );
            }
        };

        Event::on(ActiveRecord::class, ActiveRecord::EVENT_AFTER_DELETE, $funcDelete);
    }
}
