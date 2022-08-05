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
        if ($clientChatForm = ClientChatForm::find()->where(['ccf_key' => $key])->one()) {
            return $clientChatForm;
        }
        return null;
    }
}
