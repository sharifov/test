<?php

namespace sales\model\clientChatRequest\useCase\api\create\requestEventCreator;

use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\useCase\api\create\ClientChatRequestApiForm;
use sales\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;
use sales\model\clientChatRequest\useCase\api\create\requestEvent\GuestDisconnectedEvent;

/**
 * Class GuestDisconnectedEventCreator
 * @package sales\model\clientChatRequest\useCase\api\create\requestEventCreator
 */
class GuestDisconnectedEventCreator extends ChatRequestEventCreator
{
    public function getEvent(): ChatRequestEvent
    {
        return \Yii::createObject(GuestDisconnectedEvent::class);
    }

    public function handle(ClientChatRequestApiForm $form): void
    {
        $this->clientChatRequest = ClientChatRequest::createByApi($form);
        $chatRequestEvent = $this->getEvent();
        $chatRequestEvent->process($this->clientChatRequest);
    }
}
