<?php

namespace src\model\clientChatForm\entity;

class ClientChatFormQuery
{
    /**
     * @param string $key
     * @return ClientChatForm|null
     */
    public static function getByKey(string $key): ?ClientChatForm
    {
        return ClientChatForm::find()->where(['ccf_key' => $key])->limit(1)->one();
    }
}
