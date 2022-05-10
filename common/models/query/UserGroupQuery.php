<?php

namespace common\models\query;

use common\models\UserGroup;
use common\models\UserGroupAssign;
use yii\helpers\ArrayHelper;

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

    public static function getListByUser(int $id): array
    {
        $data = UserGroup::find()
            ->join('inner', UserGroupAssign::tableName(), ['ug_id' => 'ugs_user_id'])
            ->andWhere(['ugs_user_id' => $id])
            ->orderBy(['ug_name' => SORT_ASC])
            ->asArray()
            ->all();
        return ArrayHelper::map($data, 'ug_id', 'ug_name');
    }
}
