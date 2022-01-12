<?php

namespace src\model\clientChatRequest\useCase\api\create\requestEventCreator;

use src\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;
use src\model\clientChatRequest\useCase\api\create\requestEvent\TrackEvent;

class TrackEventCreator extends ChatRequestEventCreator
{
    public function getEvent(): ChatRequestEvent
    {
        return \Yii::createObject(TrackEvent::class);
    }
}
