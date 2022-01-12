<?php

namespace sales\rbac\rules\globalRules\clientChat;

use sales\model\clientChat\entity\ClientChat;
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
