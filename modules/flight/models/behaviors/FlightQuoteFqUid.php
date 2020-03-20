<?php

namespace modules\flight\models\behaviors;

use modules\flight\models\FlightQuote;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class FlightQuoteFqUid
 * Note: Attached to FlightQuote
 */
class FlightQuoteFqUid extends Behavior
{

    /* TODO::
    При создании flight_quote в beforeSave генерируем уникальный string (uniqueid), если он не пустой.
    При Update, проверяем, если поле пустое, то генерим fq_uid и сохраняем, если не передается. (текущая логика уже есть в примерах моделей)
     */

    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
            //ActiveRecord::EVENT_AFTER_UPDATE => 'customAfterSave',
            //ActiveRecord::EVENT_AFTER_INSERT => 'customAfterSave',
        ];
    }

    /**
     * @param $event
     */
    public function customAfterSave($event): void
    {
        if (array_key_exists('pq_profit_amount', $event->changedAttributes) ) {

        }
    }

    public function beforeInsert(): void
    {
        $this->owner->fq_uid = 0.00; // if need
        $this->uid = empty($this->owner->fq_uid) ? uniqid('fq_uid', true) : $this->owner->fq_uid;
    }

    public function beforeUpdate(): void
    {
        // example
    }
}   
