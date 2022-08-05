<?php

namespace src\model\clientChatForm;

use src\model\clientChatForm\entity\ClientChatForm;

/**
 * Class ClientChatFormRepository
 * @package src\model\clientChatForm`
 */
class ClientChatFormRepository
{
    /**
     * @param string $key
     * @return ClientChatForm|null
     */
    public function getByKey(string $key): ?ClientChatForm
    {
        if ($clientChatForm = ClientChatForm::find()->where(['ccf_key' => $key])->one()) {
            return $clientChatForm;
        }
        return null;
    }
}
