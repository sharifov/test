<?php

namespace src\model\callTerminateLog\entity;

/**
* @see CallTerminateLog
*/
class CallTerminateLogScopes extends \yii\db\ActiveQuery
{
    /**
    * @return CallTerminateLog[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return CallTerminateLog|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
