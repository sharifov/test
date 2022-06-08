<?php

namespace src\behaviors;

use common\models\DateSensitive;
use src\services\dateSensitive\DateSensitiveService;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveRecord;

class DateSensitiveBehavior extends Behavior
{
    /**
     * @var DateSensitive the owner of this behavior
     */
    public $owner;

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'reInitViews',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'reInitViews',
            ActiveRecord::EVENT_BEFORE_DELETE => 'dropViews',
        ];
    }

    public function reInitViews(Event $event)
    {
        if (
            $event->name == ActiveRecord::EVENT_BEFORE_UPDATE
            && empty($this->owner->dirtyAttributes)
        ) {
            return;
        }

        $service = \Yii::createObject(DateSensitiveService::class);
        $service->createViews($this->owner);
    }

    public function dropViews(Event $event)
    {
        $service = \Yii::createObject(DateSensitiveService::class);
        $service->dropViews($this->owner);
    }
}
