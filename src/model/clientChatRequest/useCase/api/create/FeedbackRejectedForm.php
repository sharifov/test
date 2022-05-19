<?php

namespace src\model\clientChatRequest\useCase\api\create;

use common\models\ClientChatSurvey;
use src\model\clientChat\entity\ClientChat;

/**
 * Class FeedbackRejectedForm
 * @package src\model\clientChatRequest\useCase\api\create
 */
class FeedbackRejectedForm extends FeedbackFormBase
{
    /**
     * @param ClientChat $clientChat
     * @return bool
     */
    public function syncWithDb(ClientChat $clientChat): bool
    {
        $result = ClientChatSurvey::updateAll(['ccs_status' => ClientChatSurvey::STATUS_REJECT], 'ccs_client_chat_id=:client_chat_id', [':client_chat_id' => $clientChat->cch_id]);
        return $result > 0;
    }
}
