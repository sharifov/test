<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[QuoteStatusLog]].
 *
 * @see QuoteStatusLog
 */
class QuoteStatusLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return QuoteStatusLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return QuoteStatusLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
