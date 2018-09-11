<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[AirportList]].
 *
 * @see AirportList
 */
class AirportListQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return AirportList[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return AirportList|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
