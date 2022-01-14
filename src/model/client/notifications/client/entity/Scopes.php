<?php

namespace src\model\client\notifications\client\entity;

/**
* @see ClientNotification
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return ClientNotification[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ClientNotification|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
