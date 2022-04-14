<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleType;

/**
 * This is the ActiveQuery class for [[ShiftScheduleType]].
 *
 * @see ShiftScheduleType
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ShiftScheduleType[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ShiftScheduleType|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
