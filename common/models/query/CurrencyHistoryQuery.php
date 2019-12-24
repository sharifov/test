<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\CurrencyHistory]].
 *
 * @see \common\models\CurrencyHistory
 */
class CurrencyHistoryQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return \common\models\CurrencyHistory[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return \common\models\CurrencyHistory|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
