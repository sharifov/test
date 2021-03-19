<?php

namespace sales\model\clientChatRequest\useCase\api\create\requestEventCreator;

use sales\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;
use sales\model\clientChatRequest\useCase\api\create\requestEvent\TrackEvent;

class TrackEventCreator extends ChatRequestEventCreator
{
    public function getEvent(): ChatRequestEvent
    {
        return \Yii::createObject(TrackEvent::class);
    }
}
