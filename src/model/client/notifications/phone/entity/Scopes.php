<?php

namespace src\model\client\notifications\phone\entity;

/**
* @see ClientNotificationPhoneList
*/
class Scopes extends \yii\db\ActiveQuery
{
    public function new(): self
    {
        return $this->andWhere(['cnfl_status_id' => Status::NEW]);
    }

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
