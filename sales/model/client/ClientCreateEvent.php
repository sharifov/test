<?php

namespace sales\model\client;

use yii\base\Component;
use yii\base\Event;

class ClientCreateEvent extends Component
{
    public const CREATE = 'create';

    public static function createByClientChatRequest(Event $event): void
    {
        $clientChatRequest = $event->data;

        var_dump($clientChatRequest);
        die;
    }
}
