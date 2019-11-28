<?php

namespace common\models\query;

use common\models\ApiLog;

/**
 * This is the ActiveQuery class for [[ApiLog]].
 *
 * @see ApiLog
 */
class ApiLogQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ApiLog[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ApiLog|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
