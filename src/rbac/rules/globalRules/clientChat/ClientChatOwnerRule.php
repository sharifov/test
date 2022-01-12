<?php

namespace src\rbac\rules\globalRules\clientChat;

use src\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatOwnerRule extends Rule
{
    public $name = 'ClientChatOwnerRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }
        $chat = $params['chat'];

        return $chat->isOwner((int)$user);
    }
}
