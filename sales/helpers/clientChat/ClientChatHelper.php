<?php

namespace sales\helpers\clientChat;

use common\models\Employee;
use common\models\UserProfile;
use frontend\helpers\JsonHelper;
use sales\auth\Auth;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\clientChatUserChannel\entity\search\ClientChatUserChannelSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\VarDumper;

class ClientChatHelper
{
    public static function getClientName(ClientChat $clientChat): string
    {
        return $clientChat->cchClient && $clientChat->cchClient->full_name ? $clientChat->cchClient->full_name : 'Client-' . $clientChat->cch_id;
    }

    public static function getFirstLetterFromName(string $name): string
    {
        return mb_strtoupper(mb_substr($name, 0, 1));
    }

    public static function getClientStatusMessage(ClientChat $clientChat): string
    {
        return (int)$clientChat->cch_client_online ? ' is online...' : ' left from chat...';
    }

    public static function getAvailableAgentForTransfer(ClientChat $chat, ?int $depId): array
    {
        $result = [];
        if ($channel = ClientChatChannel::find()->select(['ccc_id'])->byDepartment($depId)->byProject($chat->cch_project_id)->asArray()->one()) {
            $search = new ClientChatUserChannelSearch();
            $availableAgents = $search->getAvailableAgentForTransfer((int)$channel['ccc_id']);
            $result = ArrayHelper::map($availableAgents, 'user_id', 'nickname');
        }
        return $result;
    }

    public static function isShowInput(?ClientChat $clientChat, ?Employee $employee): bool
    {
        if ($clientChat === null || $employee === null) {
            return false;
        }
        if ($clientChat->isInClosedStatusGroup() || !$clientChat->isOwner($employee->getId())) {
            return false;
        }
        return true;
    }

    public static function isDialogReadOnly($clientChat, ?Employee $employee): bool
    {
        if ($clientChat === null || $employee === null) {
            return true;
        }

        if ($clientChat instanceof ClientChat) {
            return $clientChat->isInClosedStatusGroup() || !$clientChat->isOwner($employee->getId());
        }


        if (is_array($clientChat)) {
            $chatInClosedGroup = ArrayHelper::isIn((int)$clientChat['cch_status_id'], ClientChat::CLOSED_STATUS_GROUP);
            $isUserOwner = (int)$clientChat['cch_owner_user_id'] === $employee->getId();
            return $chatInClosedGroup || !$isUserOwner;
        }

        return true;
    }

    public static function displayBtnAcceptTransfer(Employee $user, $accessId, $chatId, $accessUrl, $accessAction): string
    {
        if (!$user->can('client-chat/accept-transfer')) {
            return '';
        }
        return Html::button('<i class="fa fa-check"></i> Accept', [
            'class' => 'btn btn-sm btn-success _cc-access-action',
            'data-ccua-id' => $accessId,
            'data-cch-id' => $chatId,
            'data-ajax-url' => $accessUrl,
            'data-access-action' => $accessAction
        ]);
    }

    public static function displayBtnSkipTransfer(Employee $user, $accessId, $chatId, $accessUrl, $accessAction): string
    {
        if (!$user->can('client-chat/skip-transfer')) {
            return '';
        }
        return Html::button('<i class="fa fa-close"></i> Skip', [
            'class' => 'btn btn-sm btn-warning _cc-access-action',
            'data-ccua-id' => $accessId,
            'data-cch-id' => $chatId,
            'data-ajax-url' => $accessUrl,
            'data-access-action' => $accessAction
        ]);
    }

    public static function displayBtnAcceptPending(Employee $user, $accessId, $chatId, $accessUrl, $accessAction): string
    {
        if (!$user->can('client-chat/accept-pending')) {
            return '';
        }
        return Html::button('<i class="fa fa-check"></i> Accept', [
            'class' => 'btn btn-sm btn-success _cc-access-action',
            'data-ccua-id' => $accessId,
            'data-cch-id' => $chatId,
            'data-ajax-url' => $accessUrl,
            'data-access-action' => $accessAction
        ]);
    }

    public static function displayBtnSkipPending(Employee $user, $accessId, $chatId, $accessUrl, $accessAction): string
    {
        if (!$user->can('client-chat/skip-pending')) {
            return '';
        }
        return Html::button('<i class="fa fa-close"></i> Skip', [
            'class' => 'btn btn-sm btn-warning _cc-access-action',
            'data-ccua-id' => $accessId,
            'data-cch-id' => $chatId,
            'data-ajax-url' => $accessUrl,
            'data-access-action' => $accessAction
        ]);
    }

    public static function displayBtnTakeIdle(Employee $user, array $access, $accessUrl, $accessAction): string
    {
        $chat = new ClientChat();
        $chat->cch_status_id = (int)($access['cch_status_id'] ?? 0);
        $chat->cch_owner_user_id = (int)($access['ccua_user_id'] ?? 0);

        if (!$user->can('client-chat/view', ['chat' => $chat]) || !$user->can('client-chat/take', ['chat' => $chat])) {
            return '';
        }
        return Html::button('<i class="fa fa-check"></i> Take', [
            'class' => 'btn btn-sm btn-info _cc-access-action',
            'data-ccua-id' => $access['ccua_id'] ?? null,
            'data-cch-id' => $access['ccua_cch_id'] ?? null,
            'data-ajax-url' => $accessUrl,
            'data-access-action' => $accessAction
        ]);
    }

    public static function formatOffsetUtcToLeadOffsetGmt(?string $offset): ?string
    {
        if ($offset) {
            if (preg_match('/^([+\-])[0-9]{4}$/', $offset)) {
                return substr_replace($offset, '.', 3, 0);
            }
            \Yii::error('Offset format is not valid: ' . $offset, 'ClientChatHelper::formatOffsetUtcToLeadOffsetGmt');
            return null;
        }
        return null;
    }

    public static function prepareChatIds(string $sourceIds): string
    {
        if (!$ids = JsonHelper::decode($sourceIds)) {
            return '[]';
        }

        $ids = array_filter($ids, static function ($value) {
            return !is_null($value);
        });
        return $ids ? JsonHelper::encode($ids) : '[]';
    }
}
