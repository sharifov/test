<?php

namespace sales\model\clientChatRequest\useCase\api\create\requestEventCreator;

use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use sales\model\clientChatRequest\useCase\api\create\requestEvent\AgentUtteredEvent;
use sales\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;

class AgentUtteredEventCreator extends ChatRequestEventCreator
{
    public function getEvent(): ChatRequestEvent
    {
        return \Yii::createObject(AgentUtteredEvent::class);
    }

    public function handle(ClientChatRequestApiForm $form): void
    {
        $this->clientChatRequest = ClientChatRequest::createByApi($form);

        $chatRequestEvent = $this->getEvent();
        $chatRequestEvent->form = $form;
        $chatRequestEvent->process($this->clientChatRequest);
    }
}
