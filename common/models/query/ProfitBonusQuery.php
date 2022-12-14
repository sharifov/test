<?php

namespace common\models\query;

use common\models\ProfitBonus;

/**
 * This is the ActiveQuery class for [[ProfitBonus]].
 *
 * @see ProfitBonus
 */
class ProfitBonusQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return ProfitBonus[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return ProfitBonus|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
