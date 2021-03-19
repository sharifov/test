<?php

namespace sales\model\clientChatRequest\useCase\api\create\requestEvent;

use sales\model\clientChatRequest\entity\ClientChatRequest;

interface ChatRequestEvent
{
    public function process(ClientChatRequest $entity): void;

    public function getClassName(): string;
}
