<?php

namespace src\model\clientChatFormResponse;

use src\helpers\ErrorsToStringHelper;
use src\model\clientChatFormResponse\entity\ClientChatFormResponse;

/**
 * Class ClientChatFeedbackRepository
 * @package src\model\clientChatFeedback`
 */
class ClientChatFormResponseRepository
{
    public function checkDuplicateValue(int $formId, int $clientChatId, string $value): bool
    {
        return ClientChatFormResponse::find()
            ->where([
                'ccfr_form_id' => $formId,
                'ccfr_client_chat_id' => $clientChatId,
                'ccfr_value' => $value
            ])->exists();
    }

    public function save(ClientChatFormResponse $model): ClientChatFormResponse
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model), -1);
        }
        return $model;
    }
}
