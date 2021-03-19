<?php

namespace sales\model\clientChatRequest\useCase\api\create\requestEventCreator;

use common\models\Notifications;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\useCase\create\ClientChatRepository;
use sales\model\clientChatRequest\entity\ClientChatRequest;
use sales\model\clientChatRequest\useCase\api\create\requestEvent\ChatRequestEvent;
use sales\model\clientChatRequest\useCase\api\create\requestEvent\RoomConnectedEvent;
use sales\model\clientChatStatusLog\entity\ClientChatStatusLog;
use sales\services\client\ClientManageService;
use sales\services\clientChatService\ClientChatService;
use yii\helpers\Html;

class RoomConnectedEventCreator extends ChatRequestEventCreator
{
    public function getEvent(): ChatRequestEvent
    {
        return \Yii::createObject(RoomConnectedEvent::class);
    }
}
