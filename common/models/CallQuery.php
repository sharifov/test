<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Call]].
 *
 * @see Call
 */
class CallQuery extends \yii\db\ActiveQuery
{
    /**
     * @param int $parentId
     * @return CallQuery
     */
    public function firstChild(int $parentId): CallQuery
    {
        return $this->andWhere(['c_parent_id' => $parentId])->orderBy(['c_id' => SORT_ASC])->limit(1);
    }

    /**
     * @param int $parentId
     * @return CallQuery
     */
    public function lastChild(int $parentId): CallQuery
    {
        return $this->andWhere(['c_parent_id' => $parentId])->orderBy(['c_id' => SORT_DESC])->limit(1);
    }
}
