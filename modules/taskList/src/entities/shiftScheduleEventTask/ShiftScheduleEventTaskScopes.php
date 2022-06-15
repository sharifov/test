<?php

namespace modules\taskList\src\entities\shiftScheduleEventTask;

/**
* @see ShiftScheduleEventTask
*/
class ShiftScheduleEventTaskScopes extends \yii\db\ActiveQuery
{
    /**
    * @return ShiftScheduleEventTask[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ShiftScheduleEventTask|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
