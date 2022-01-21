<?php

namespace src\model\userData\entity;

/**
* @see UserData
*/
class Scopes extends \yii\db\ActiveQuery
{
    /**
    * @return UserData[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return UserData|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
