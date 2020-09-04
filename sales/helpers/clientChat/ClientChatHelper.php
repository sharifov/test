<?php
namespace sales\helpers\clientChat;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;

class ClientChatHelper
{
	public static function getClientName(ClientChat $clientChat): string
	{
		return $clientChat->cchClient && $clientChat->cchClient->full_name ? $clientChat->cchClient->full_name : 'Guest-' . $clientChat->cch_id;
	}

	public static function getFirstLetterFromName(ClientChat $clientChat): string
	{
		return mb_strtoupper(mb_substr(self::getClientName($clientChat), 0, 1));
	}

	public static function getClientStatusMessage(ClientChat $clientChat): string
	{
		return (int)$clientChat->cch_client_online ? ' is online...' : ' left from chat...';
	}

	public static function getAvailableAgentForTransfer(ClientChat $chat, $depId): array
	{
		$result = [];
		if ($channel = ClientChatChannel::find()->byDepartment($depId)->byProject($chat->cch_project_id)->one()) {
			if ($userChannel = ClientChatUserChannel::find()->byChannelId($channel->ccc_id)->all()) {
				/** @var ClientChatUserChannel $item */
				foreach ($userChannel as $item) {
					if ($item->ccucUser->userProfile && $item->ccucUser->userProfile->isRegisteredInRc()) {
						$result[$item->ccuc_user_id] = $item->ccucUser->nickname ?: $item->ccucUser->username;
					}
				}
			}

		}
		return $result;
	}
}