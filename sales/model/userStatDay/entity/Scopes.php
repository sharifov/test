<?php

namespace sales\model\userStatDay\entity;

/**
 * This is the ActiveQuery class for [[UserStatDay]].
 *
 * @see UserStatDay
 */
class Scopes extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserStatDay[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserStatDay|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function grossProfit(): self
    {
        return $this->andWhere(['usd_key' => UserStatDayKey::GROSS_PROFIT]);
    }
}
