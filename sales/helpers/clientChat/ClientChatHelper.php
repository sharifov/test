<?php
namespace sales\helpers\clientChat;

use sales\model\clientChat\entity\ClientChat;

class ClientChatHelper
{
	public static function getClientName(ClientChat $clientChat): string
	{
		return $clientChat->cchClient && $clientChat->cchClient->full_name ? $clientChat->cchClient->full_name : 'Guest-' . $clientChat->cch_id;
	}

	public static function getClientStatusMessage(ClientChat $clientChat): string
	{
		return (int)$clientChat->cch_client_online ? ' is online...' : ' left from chat...';
	}
}