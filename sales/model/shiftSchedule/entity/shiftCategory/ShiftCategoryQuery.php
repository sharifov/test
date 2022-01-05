<?php

namespace sales\model\shiftSchedule\entity\shiftCategory;

/**
 * This is the ActiveQuery class for [[ShiftCategory]].
 *
 * @see ShiftCategory
 */
class ShiftCategoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ShiftCategory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ShiftCategory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
