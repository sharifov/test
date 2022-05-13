<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign;

/**
 * This is the ActiveQuery class for [[ShiftScheduleTypeLabelAssign]].
 *
 * @see ShiftScheduleTypeLabelAssign
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ShiftScheduleTypeLabelAssign[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ShiftScheduleTypeLabelAssign|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
