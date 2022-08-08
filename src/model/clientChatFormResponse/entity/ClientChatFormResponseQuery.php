<?php

namespace src\model\clientChatFormResponse\entity;

class ClientChatFormResponseQuery
{
    /**
     * @param int $formId
     * @param int $clientChatId
     * @param string $value
     * @return bool
     */
    public static function checkDuplicateValue(int $formId, int $clientChatId, string $value): bool
    {
        return ClientChatFormResponse::find()
            ->where([
                'ccfr_form_id' => $formId,
                'ccfr_client_chat_id' => $clientChatId,
                'ccfr_value' => $value
            ])->exists();
    }
}
