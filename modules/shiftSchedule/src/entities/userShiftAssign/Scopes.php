<?php

namespace modules\shiftSchedule\src\entities\userShiftAssign;

/**
* @see UserShiftAssign
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return UserShiftAssign[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return UserShiftAssign|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
