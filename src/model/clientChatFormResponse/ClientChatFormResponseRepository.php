<?php

namespace src\model\clientChatFormResponse;

use src\model\clientChat\entity\ClientChat;
use src\model\clientChatRequest\useCase\api\create\ClientChatFormResponseApiForm;

/**
 * Class ClientChatFeedbackRepository
 * @package src\model\clientChatFeedback`
 */
class ClientChatFormResponseRepository
{
    public function save(ClientChatFormResponseApiForm $form, ClientChat $clientChat): ClientChatFormResponseApiForm
    {
        if ($form->syncWithDb($clientChat) === false) {
            throw new \RuntimeException('Client Chat Form saving failed');
        }

        return $form;
    }
}
