<?php

namespace frontend\widgets\clientChat;

use common\models\Employee;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\ClientChatQuery;
use yii\helpers\Url;

class ClientChatAccessMessage
{
    private const COMMAND_ACCEPT = 'accept';
    private const COMMAND_PENDING = 'pending';
    private const COMMAND_SKIP = 'skip';
    private const COMMAND_DELETED = 'deleted';
    private const COMMAND_RESET = 'reset';
    private const COMMAND_ACCEPT_TRANSFER = 'accept_transfer';
    private const COMMAND_TAKE = 'take';

    public static function accept(int $chatId, int $userId, int $chatUserAccessId): array
    {
        return [
            'command' => self::COMMAND_ACCEPT,
            'url' => Url::toRoute(['/client-chat/index', 'chid' => $chatId]),
            'userId' => $userId,
            'chatId' => $chatId,
            'chatUserAccessId' => $chatUserAccessId
        ];
    }

    public static function pending(int $userId, int $chatUserAccess): array
    {
        return [
            'command' => self::COMMAND_PENDING,
            'item' => self::getOneRequest($userId, $chatUserAccess)
        ];
    }

    public static function skip(int $cchId, int $userId, int $chatUserAccessId): array
    {
        return [
            'command' => self::COMMAND_SKIP,
            'userId' => $userId,
            'chatId' => $cchId,
            'chatUserAccessId' => $chatUserAccessId
        ];
    }

    public static function reset(int $userId): array
    {
        $data = self::refresh($userId);
        return [
            'command' => self::COMMAND_RESET,
            'items' => $data['items'],
            'totalItems' => $data['totalItems']
        ];
    }

    public static function deleted(int $cchId, int $userId, int $chatUserAccessId): array
    {
        return [
            'command' => self::COMMAND_DELETED,
            'status_id' => null,
            'userId' => $userId,
            'chatId' => $cchId,
            'chatUserAccessId' => $chatUserAccessId,
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

    public static function chatCanceledTransfer(ClientChat $chat, ?Employee $user): array
    {
        $message = 'The chat transfer was canceled by the system';
        if ($user) {
            $client = $chat->cchClient;
            $message = 'User: ' . $user->nickname . ' canceled chat transfer with client: ' . $client->full_name;
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

    public static function chatReopen(int $chatId, string $userNickname): array
    {
        return [
            'message' => 'User: ' . $userNickname . ' reopen this chat',
            'cchId' => $chatId,
        ];
    }

    public static function chatAutoReturn(int $chatId): array
    {
        return [
            'message' => 'The chat has been returned to In Progress.',
            'cchId' => $chatId,
        ];
    }

    public static function chatAutoReopen(int $chatId): array
    {
        return [
            'message' => 'This chat is auto reopen. Client wrote a message.',
            'cchId' => $chatId,
        ];
    }

    public static function chatTaken(ClientChat $chat, string $newOwnerNickname): array
    {
        return [
            'message' => 'Chat was taken by ' . $newOwnerNickname ,
            'cchId' => $chat->cch_id,
            'tab' => ClientChat::TAB_ACTIVE
        ];
    }

    public static function chatTakenBy(int $chatId, string $newOwnerNickname, string $supervisorNickname): array
    {
        return [
            'message' => $supervisorNickname . ' has take your Client Chat to ' . $newOwnerNickname,
            'cchId' => $chatId,
        ];
    }

    public static function chatArchive(int $chatId): array
    {
        return [
            'message' => 'Chat changed status to ' . ClientChat::getStatusNameById(ClientChat::STATUS_ARCHIVE),
            'cchId' => $chatId,
        ];
    }

    public static function chatInProgress(int $chatId): array
    {
        return [
            'message' => 'Chat changed status to ' . ClientChat::getStatusNameById(ClientChat::STATUS_IN_PROGRESS),
            'cchId' => $chatId,
        ];
    }

    public static function chatIdle(int $chatId): array
    {
        return [
            'message' => 'Chat changed status to ' . ClientChat::getStatusNameById(ClientChat::STATUS_IDLE),
            'cchId' => $chatId,
        ];
    }

    private static function refresh(int $userId): array
    {
        $widget = ClientChatAccessWidget::getInstance();
        $widget->userId = $userId;
        return [
            'items' => $widget->fetchItems(),
            'totalItems' => $widget->getTotalItems()
        ];
    }

    private static function getOneRequest(int $userId, int $chatUserAccessId): array
    {
        $widget = ClientChatAccessWidget::getInstance();
        $widget->userId = $userId;
        $widget->open = true;
        $widget->userAccessId = $chatUserAccessId;
        return $widget->fetchOneItem();
    }

    public static function acceptTransfer(int $chatId, int $userId, int $chatUserAccessId): array
    {
        return [
            'command' => self::COMMAND_ACCEPT_TRANSFER,
            'userId' => $userId,
            'chatId' => $chatId,
            'chatUserAccessId' => $chatUserAccessId
        ];
    }

    public static function take(int $chatId, int $userId, int $chatUserAccessId): array
    {
        return [
            'command' => self::COMMAND_TAKE,
            'userId' => $userId,
            'chatId' => $chatId,
            'chatUserAccessId' => $chatUserAccessId
        ];
    }
}
