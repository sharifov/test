<?php

namespace modules\flight\models\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class FlightQuoteFqUid
 * Note: Attached to FlightQuote
 */
class FlightQuoteFqUid extends Behavior
{
    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'generateUid',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'generateUid',
        ];
    }

    public function generateUid(): void
    {
        $this->owner->fq_uid = empty($this->owner->fq_uid) ? uniqid('', true) : $this->owner->fq_uid;
    }
}   
