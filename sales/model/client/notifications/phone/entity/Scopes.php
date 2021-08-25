<?php

namespace sales\model\client\notifications\phone\entity;

/**
* @see ClientNotificationPhoneList
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return ClientNotificationPhoneList[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ClientNotificationPhoneList|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
