<?php
namespace frontend\widgets\clientChat;

use common\models\Employee;
use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use yii\helpers\Url;

class ClientChatAccessMessage
{
	private const COMMAND_ACCEPT = 'accept';
	private const COMMAND_PENDING = 'pending';
	private const COMMAND_SKIP = 'skip';
	private const COMMAND_DELETED = 'deleted';

	public static function accept(ClientChatUserAccess $access): array
	{
		return [
			'command' => self::COMMAND_ACCEPT,
			'url' => Url::toRoute(['/client-chat/index', 'chid' => $access->ccuaCch->cch_id]),
			'status_id' => $access->ccua_status_id,
			'user_id' => $access->ccua_user_id,
			'cch_id' => $access->ccua_cch_id,
			'pjaxUrl' => Url::to('/client-chat/pjax-update-chat-widget'),
			'html' => self::refresh($access->ccua_user_id)
		];
	}

	public static function pending(ClientChatUserAccess $access): array
	{
		return [
			'command' => self::COMMAND_PENDING,
			'status_id' => $access->ccua_status_id,
			'user_id' => $access->ccua_user_id,
			'cch_id' => $access->ccua_cch_id,
			'pjaxUrl' => Url::to('/client-chat/pjax-update-chat-widget'),
			'html' => self::refresh($access->ccua_user_id, $access->ccua_id)
		];
	}

	public static function skip(ClientChatUserAccess $access): array
	{
		return [
			'command' => self::COMMAND_SKIP,
			'status_id' => $access->ccua_status_id,
			'user_id' => $access->ccua_user_id,
			'cch_id' => $access->ccua_cch_id,
			'pjaxUrl' => Url::to('/client-chat/pjax-update-chat-widget'),
			'html' => self::refresh($access->ccua_user_id)
		];
	}

	public static function deleted(int $userId): array
	{
		return [
			'command' => self::COMMAND_DELETED,
			'status_id' => null,
			'user_id' => $userId,
			'cch_id' => null,
			'pjaxUrl' => Url::to('/client-chat/pjax-update-chat-widget'),
			'html' => self::refresh($userId)
		];
	}

	public static function allAgentsCanceledTransfer(ClientChat $chat): array
	{
		return [
			'message' => 'All users rejected the chat with id: ' . $chat->cch_id,
			'cchId' => $chat->cch_id,
			'tab' => ClientChat::TAB_ACTIVE
		];
	}

	public static function agentTransferAccepted(ClientChat $chat, Employee $employee): array
	{
		return [
			'message' => 'User: ' . $employee->nickname . ' accepted chat with id: ' . $chat->cch_id,
			'cchId' => $chat->cch_id,
			'tab' => ClientChat::TAB_ARCHIVE
		];
	}

	private static function refresh(int $userId, ?int $accessId = null)
	{
		$widget = ClientChatAccessWidget::getInstance();
		$widget->userId = $userId;
		$widget->open = true;
		$widget->userAccessId = $accessId;
		return $widget->run();
	}
}