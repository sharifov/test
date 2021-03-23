<?php

namespace sales\model\clientChat\socket;

use common\models\Notifications;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use yii\helpers\Url;

class ClientChatSocketCommands
{
    public static function clientChatAddQuotesButton(ClientChat $chat, int $leadId): void
    {
        Notifications::pub(
            [ClientChatChannel::getPubSubKey($chat->cch_channel_id)],
            'clientChatAddQuoteButton',
            ['data' => ['chatId' => $chat->cch_id, 'leadId' => $leadId, 'url' => Url::toRoute('/client-chat/send-quote-list')]]
        );
    }

    public static function clientChatRemoveQuotesButton(ClientChat $chat, int $leadId): void
    {
        Notifications::pub(
            [ClientChatChannel::getPubSubKey($chat->cch_channel_id)],
            'clientChatRemoveQuoteButton',
            ['data' => ['chatId' => $chat->cch_id, 'leadId' => $leadId]]
        );
    }

    public static function clientChatAddOfferButton(ClientChat $chat, int $leadId): void
    {
        Notifications::pub(
            [ClientChatChannel::getPubSubKey($chat->cch_channel_id)],
            'clientChatAddOfferButton',
            ['data' => ['chatId' => $chat->cch_id, 'leadId' => $leadId, 'url' => Url::toRoute('/client-chat/send-offer-list')]]
        );
    }

    public static function clientChatRemoveOfferButton(ClientChat $chat, int $leadId): void
    {
        Notifications::pub(
            [ClientChatChannel::getPubSubKey($chat->cch_channel_id)],
            'clientChatRemoveOfferButton',
            ['data' => ['chatId' => $chat->cch_id, 'leadId' => $leadId]]
        );
    }
}
