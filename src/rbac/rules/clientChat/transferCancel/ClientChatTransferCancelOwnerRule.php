<?php

namespace src\rbac\rules\clientChat\transferCancel;

use src\model\clientChat\entity\ClientChat;
use src\repositories\clientChatStatusLogRepository\ClientChatStatusLogRepository;
use yii\rbac\Rule;

class ClientChatTransferCancelOwnerRule extends Rule
{
    public $name = 'ClientChatTransferCancelOwnerRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }

        /** @var ClientChat $chat */
        $chat = $params['chat'];

        $previousOwnerId = (\Yii::createObject(ClientChatStatusLogRepository::class))->getPreviousOwnerId($chat->cch_id);

        return $previousOwnerId ? ($previousOwnerId === (int)$user) : false;
    }
}
