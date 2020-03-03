<?php

namespace common\models\query;

use common\models\UserGroup;

/**
 * Class UserGroupQuery
 *
 * @see UserGroup
 */
class UserGroupQuery extends \yii\db\ActiveQuery
{
    public function enabled(): self
    {
        return $this->andWhere(['ug_disable' => 0]);
    }
}
