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

    /**
     * @return Scopes
     */
    public function excludeDeleteStatus(): Scopes
    {
        return $this->andWhere(['<>', 'uss_status_id', UserShiftSchedule::STATUS_DELETED]);
    }
}
