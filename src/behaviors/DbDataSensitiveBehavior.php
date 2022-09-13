<?php

namespace src\behaviors;

use src\model\dbDataSensitive\entity\DbDataSensitive;
use src\model\dbDataSensitiveView\entity\DbDataSensitiveView;
use src\model\dbDataSensitive\service\DbDataSensitiveService;
use yii\base\Behavior;
use yii\base\Event;
use yii\db\ActiveRecord;

class DbDataSensitiveBehavior extends Behavior
{
    /**
     * @var DbDataSensitive the owner of this behavior
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

        $service = \Yii::createObject(DbDataSensitiveService::class);
        $dbDataSensitiveViews = DbDataSensitiveView::find()->andWhere(['ddv_dda_id' => $this->owner->dda_id])->all();
        foreach ($dbDataSensitiveViews as $dataSensitiveView) {
            $service->dropViewByDbDataSensitiveView($dataSensitiveView);
        }

        $service->createViews($this->owner);
    }

    public function dropViews(Event $event)
    {
        $service = \Yii::createObject(DbDataSensitiveService::class);
        $service->dropViews($this->owner);
    }
}
