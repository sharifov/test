<?php

namespace src\model\clientChat\event\listener;

use common\models\Notifications;
use src\model\clientChat\event\ClientChatArchiveEvent;
use src\model\clientChatChannel\entity\ClientChatChannel;

class ClientChatRefreshListListener
{
    public function handle(ClientChatArchiveEvent $event): void
    {
        Notifications::pub(
            [ClientChatChannel::getPubSubKey($event->prevChannelId)],
            'reloadClientChatList'
        );
    }
}
