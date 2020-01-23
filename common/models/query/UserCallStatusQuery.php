<?php

namespace common\models\query;

use common\models\UserCallStatus;

/**
 * This is the ActiveQuery class for [[UserCallStatus]].
 *
 * @see UserCallStatus
 */
class UserCallStatusQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return UserCallStatus[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return UserCallStatus|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
