<?php

namespace sales\model\userClientChatData\entity;

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
}
