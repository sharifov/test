<?php

namespace src\model\clientChatFeedback;

use src\model\clientChatFeedback\entity\ClientChatFeedback;
use src\repositories\NotFoundException;

/**
 * Class ClientChatFeedbackRepository
 */
class ClientChatFeedbackRepository
{
    public function save(ClientChatFeedback $clientChatFeedback): ClientChatFeedback
    {
        if (!$clientChatFeedback->save()) {
            throw new \RuntimeException('Client Chat Feedback saving failed');
        }
        return $clientChatFeedback;
    }

    public function findById(int $id): ClientChatFeedback
    {
        if ($clientChatFeedback = ClientChatFeedback::findOne($id)) {
            return $clientChatFeedback;
        }
        throw new NotFoundException('Client chat Feedback is not found');
    }
}
