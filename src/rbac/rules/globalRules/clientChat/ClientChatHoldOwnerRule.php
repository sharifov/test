<?php

namespace src\rbac\rules\globalRules\clientChat;

use src\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

/**
 * Class ClientChatHoldOwnerRule
 */
class ClientChatHoldOwnerRule extends Rule
{
    public $name = 'ClientChatHoldOwnerRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }
        $chat = $params['chat'];

        return ($chat->isInProgress() && $chat->isOwner((int) $user));
    }
}
