<?php
namespace sales\helpers\clientChat;

use common\models\Employee;
use common\models\UserProfile;
use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChatChannel\entity\ClientChatChannel;
use sales\model\clientChatUserAccess\entity\ClientChatUserAccess;
use sales\model\clientChatUserChannel\entity\ClientChatUserChannel;
use sales\model\clientChatUserChannel\entity\search\ClientChatUserChannelSearch;
use yii\helpers\ArrayHelper;

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
}
