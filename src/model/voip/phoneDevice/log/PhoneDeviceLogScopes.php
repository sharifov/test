<?php

namespace src\model\voip\phoneDevice\log;

/**
* @see PhoneDeviceLog
*/
class PhoneDeviceLogScopes extends \yii\db\ActiveQuery
{
    /**
    * @return PhoneDeviceLog[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return PhoneDeviceLog|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
