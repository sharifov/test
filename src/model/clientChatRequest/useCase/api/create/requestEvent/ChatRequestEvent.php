<?php

namespace src\model\clientChatRequest\useCase\api\create\requestEvent;

use src\model\clientChatRequest\entity\ClientChatRequest;

interface ChatRequestEvent
{
    public function process(ClientChatRequest $entity): void;

    public function getClassName(): string;
}
