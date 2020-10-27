<?php
namespace sales\model\clientChatFeedback;

use sales\model\clientChatFeedback\entity\ClientChatFeedback;
use sales\repositories\NotFoundException;
use sales\repositories\Repository;

/**
 * Class ClientChatFeedbackRepository
 */
class ClientChatFeedbackRepository extends Repository
{
    public function save(ClientChatFeedback $ClientChatFeedback): ClientChatFeedback
    {
        if (!$ClientChatFeedback->save(false)) {
            throw new \RuntimeException('Client Chat Feedback saving failed');
        }
        return $ClientChatFeedback;
    }

    public function findById(int $id): ClientChatFeedback
    {
        if ($clientChatFeedback = ClientChatFeedback::findOne($id)) {
            return $clientChatFeedback;
        }
        throw new NotFoundException('Client chat Feedback is not found');
    }
}
