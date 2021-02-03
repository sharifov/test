<?php

namespace sales\model\shiftSchedule\entity\shiftScheduleRule;

/**
* @see ShiftScheduleRule
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return ShiftScheduleRule[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ShiftScheduleRule|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
