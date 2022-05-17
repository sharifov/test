<?php

namespace src\model\clientChatRequest\useCase\api\create;

use src\model\clientChat\entity\ClientChat;

/**
 * Interface FeedbackFormInterface
 * @package src\model\clientChatRequest\useCase\api\create
 */
interface FeedbackFormInterface
{
    /**
     * @param ClientChat $clientChat
     * @return bool
     */
    public function syncWithDb(ClientChat $clientChat): bool;
}
