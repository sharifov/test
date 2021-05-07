<?php

namespace common\models\query;

/**
* @see \common\models\PhoneBlacklistLog
*/
class PhoneBlacklistLogQuery extends \yii\db\ActiveQuery
{
    /**
    * @return \common\models\PhoneBlacklistLog[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return \common\models\PhoneBlacklistLog|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
