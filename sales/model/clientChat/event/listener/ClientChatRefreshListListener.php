<?php

namespace sales\model\clientChat\event\listener;

use common\models\Notifications;
use sales\model\clientChat\event\ClientChatArchiveEvent;
use sales\model\clientChatChannel\entity\ClientChatChannel;

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
