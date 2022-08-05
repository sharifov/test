<?php

namespace src\model\clientChatFormResponse;

use src\helpers\ErrorsToStringHelper;
use src\model\clientChatFormResponse\entity\ClientChatFormResponse;

/**
 * Class ClientChatFeedbackRepository
 * @package src\model\clientChatFormResponse`
 */
class ClientChatFormResponseRepository
{
    public function save(ClientChatFormResponse $model): ClientChatFormResponse
    {
        if (!$model->save()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($model), -1);
        }
        return $model;
    }
}
