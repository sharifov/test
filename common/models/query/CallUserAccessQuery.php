<?php

namespace common\models\query;

use common\models\CallUserAccess;

/**
 * This is the ActiveQuery class for [[CallUserAccess]].
 *
 * @see CallUserAccess
 */
class CallUserAccessQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CallUserAccess[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CallUserAccess|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
