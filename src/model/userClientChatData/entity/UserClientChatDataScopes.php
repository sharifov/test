<?php

namespace src\model\userClientChatData\entity;

/**
* @see UserClientChatDataService
*/
class UserClientChatDataScopes extends \yii\db\ActiveQuery
{
    /**
    * @return UserClientChatData[]|array
    */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
    * @return UserClientChatData|array|null
    */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byRcId(string $id): self
    {
        return $this->andWhere(['uccd_rc_user_id' => $id]);
    }
}
