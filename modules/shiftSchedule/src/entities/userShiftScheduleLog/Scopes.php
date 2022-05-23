<?php

namespace modules\shiftSchedule\src\entities\userShiftScheduleLog;

/**
 * This is the ActiveQuery class for [[UserShiftScheduleLog]].
 *
 * @see UserShiftScheduleLog
 */
class Scopes extends \yii\db\ActiveQuery
{
    /**
     * {@inheritdoc}
     * @return UserShiftScheduleLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserShiftScheduleLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
