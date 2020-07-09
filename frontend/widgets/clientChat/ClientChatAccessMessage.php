<?php
namespace frontend\widgets\clientChat;

use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\helpers\Url;

class ClientChatAccessMessage
{
	private const COMMAND_ACCEPT = 'accept';
	private const COMMAND_PENDING = 'pending';
	private const COMMAND_SKIP = 'skip';

	public static function accept(ClientChatUserAccess $access): array
	{
		return [
			'command' => self::COMMAND_ACCEPT,
			'url' => Url::toRoute(['/client-chat/index', 'chid' => $access->ccuaCch->cch_id]),
			'status_id' => $access->ccua_status_id,
			'user_id' => $access->ccua_user_id,
			'cch_id' => $access->ccua_cch_id,
			'pjaxUrl' => Url::to('/client-chat/pjax-update-chat-widget')
		];
	}

	public static function pending(ClientChatUserAccess $access): array
	{
		return [
			'command' => self::COMMAND_PENDING,
			'status_id' => $access->ccua_status_id,
			'user_id' => $access->ccua_user_id,
			'cch_id' => $access->ccua_cch_id,
			'pjaxUrl' => Url::to('/client-chat/pjax-update-chat-widget')
		];
	}

	public static function skip(ClientChatUserAccess $access): array
	{
		return [
			'command' => self::COMMAND_SKIP,
			'status_id' => $access->ccua_status_id,
			'user_id' => $access->ccua_user_id,
			'cch_id' => $access->ccua_cch_id,
			'pjaxUrl' => Url::to('/client-chat/pjax-update-chat-widget')
		];
	}
}