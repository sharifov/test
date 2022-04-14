<?php

namespace modules\shiftSchedule\src\entities\userShiftSchedule;

/**
* @see UserShiftSchedule
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return UserShiftSchedule[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return UserShiftSchedule|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
