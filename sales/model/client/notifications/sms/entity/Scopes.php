<?php

namespace sales\model\client\notifications\sms\entity;

/**
* @see ClientNotificationSmsList
*/
class Scopes extends \yii\db\ActiveQuery
{
    public function new(): self
    {
        return $this->andWhere(['cnsl_status_id' => Status::NEW]);
    }

    /**
    * @return ClientNotificationSmsList[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return ClientNotificationSmsList|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
