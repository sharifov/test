<?php

namespace src\model\client\notifications\email\entity;

/**
* @see ClientNotificationEmailList
*/
class Scopes extends \yii\db\ActiveQuery
{
    public function new(): self
    {
        return $this->andWhere(['cnel_status_id' => Status::NEW]);
    }

    /**
    * @return ClientNotificationEmailList[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ClientNotificationEmailList|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
