<?php

namespace common\models\query;

use common\models\UserParams;

/**
 * This is the ActiveQuery class for [[UserParams]].
 *
 * @see UserParams
 */
class UserParamsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserParams[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserParams|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
