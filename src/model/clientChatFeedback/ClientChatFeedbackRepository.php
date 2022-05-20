<?php

namespace src\model\clientChatFeedback;

use src\model\clientChat\entity\ClientChat;
use src\model\clientChatRequest\useCase\api\create\FeedbackFormBase;

/**
 * Class ClientChatFeedbackRepository
 * @package src\model\clientChatFeedback`
 */
class ClientChatFeedbackRepository
{
    public function save(FeedbackFormBase $feedbackForm, ClientChat $clientChat): FeedbackFormBase
    {
        if ($feedbackForm->syncWithDb($clientChat) === false) {
            throw new \RuntimeException('Client Chat Feedback saving failed');
        }
        return $feedbackForm;
    }
}
