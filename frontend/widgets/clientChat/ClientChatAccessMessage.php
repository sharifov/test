<?php
namespace frontend\widgets\clientChat;

use common\models\Employee;
use sales\model\clientChat\entity\ClientChat;
use yii\helpers\Url;

class ClientChatAccessMessage
{
	private const COMMAND_ACCEPT = 'accept';
	private const COMMAND_PENDING = 'pending';
	private const COMMAND_SKIP = 'skip';
	private const COMMAND_DELETED = 'deleted';
	private const COMMAND_RESET = 'reset';

	public static function accept(int $cchId, int $userId, int $statusId): array
	{
		return [
			'command' => self::COMMAND_ACCEPT,
			'url' => Url::toRoute(['/client-chat/index', 'chid' => $cchId]),
			'status_id' => $statusId,
			'user_id' => $userId,
			'cch_id' => $cchId,
			'pjaxUrl' => Url::to('/client-chat/pjax-update-chat-widget'),
			'html' => self::refresh($userId)
		];
	}

	public static function pending(int $cchId, int $userId, int $statusId, ?int $ccuaId, bool $isChatInTransfer): array
	{
		return [
			'command' => self::COMMAND_PENDING,
			'status_id' => $statusId,
			'user_id' => $userId,
			'cch_id' => $cchId,
			'pjaxUrl' => Url::to('/client-chat/pjax-update-chat-widget'),
			'isChatInTransfer' => $isChatInTransfer,
			'html' => self::refresh($userId, $ccuaId)
		];
	}

	public static function skip(int $cchId, int $userId, int $statusId): array
	{
		return [
			'command' => self::COMMAND_SKIP,
			'status_id' => $statusId,
			'user_id' => $userId,
			'cch_id' => $cchId,
			'pjaxUrl' => Url::to('/client-chat/pjax-update-chat-widget'),
			'html' => self::refresh($userId)
		];
	}

	public static function reset(int $userId): array
	{
		return [
			'command' => self::COMMAND_RESET,
			'user_id' => $userId,
			'pjaxUrl' => Url::to('/client-chat/pjax-update-chat-widget'),
			'html' => self::refresh($userId)
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

	public static function agentStartTransfer(ClientChat $chat, Employee $employee): array
	{
		return [
			'message' => 'User: ' . $employee->nickname . ' initialized transfer of this chat',
			'cchId' => $chat->cch_id,
			'tab' => ClientChat::TAB_ACTIVE
		];
	}

	public static function chatCanceled(ClientChat $chat, ?Employee $user): array
	{
		$message = 'The chat was canceled by the system because all agents skipped the request';
		if ($user) {
			$message = 'User: ' . $user->nickname . ' canceled transfer of this chat';
		}
		return [
			'message' => $message,
			'cchId' => $chat->cch_id,
			'tab' => ClientChat::TAB_ACTIVE
		];
	}

	public static function chatClosed(ClientChat $chat, Employee $user): array
	{
		return [
			'message' => 'User: ' . $user->nickname . ' closed this chat',
			'cchId' => $chat->cch_id,
			'tab' => ClientChat::TAB_ACTIVE
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