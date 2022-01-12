<?php

namespace src\rbac\rules\clientChat\view;

use src\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatViewEmptyRule extends Rule
{
    public $name = 'ClientChatViewEmptyRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }

        /** @var ClientChat $chat */
        $chat = $params['chat'];

        return !$chat->hasOwner();
    }
}
