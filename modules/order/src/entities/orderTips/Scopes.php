<?php

namespace modules\order\src\entities\orderTips;

/**
 * This is the ActiveQuery class for [[OrderTips]].
 *
 * @see OrderTips
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return OrderTips[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return OrderTips|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
