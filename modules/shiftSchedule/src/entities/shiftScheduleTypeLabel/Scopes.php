<?php

namespace modules\shiftSchedule\src\entities\shiftScheduleTypeLabel;

/**
 * This is the ActiveQuery class for [[ShiftScheduleTypeLabel]].
 *
 * @see ShiftScheduleTypeLabel
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ShiftScheduleTypeLabel[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ShiftScheduleTypeLabel|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
