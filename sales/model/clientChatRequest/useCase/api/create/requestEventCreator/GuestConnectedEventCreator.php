<?php

namespace sales\model\clientChatRequest\useCase\api\create\requestEventCreator;

use sales\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;
use sales\model\clientChatRequest\useCase\api\create\requestEvent\GuestConnectedEvent;

class GuestConnectedEventCreator extends ChatRequestEventCreator
{
    public function getEvent(): ChatRequestEvent
    {
        return \Yii::createObject(GuestConnectedEvent::class);
    }
}
