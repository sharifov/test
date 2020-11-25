<?php

namespace sales\model\clientChat\socket;

use common\models\Notifications;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;

class ClientChatSocketCommands
{
    public static function addClientChatAddOfferButton(ClientChat $chat, int $leadId): void
    {
        Notifications::pub(
            [ClientChatChannel::getPubSubKey($chat->cch_channel_id)],
            'clientChatAddOfferButton',
            ['data' => ['chatId' => $chat->cch_id, 'leadId' => $leadId]]
        );
    }
}
