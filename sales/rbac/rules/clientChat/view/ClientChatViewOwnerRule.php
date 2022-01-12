<?php

namespace sales\rbac\rules\clientChat\view;

use sales\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatViewOwnerRule extends Rule
{
    public $name = 'ClientChatViewOwnerRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }

        /** @var ClientChat $chat */
        $chat = $params['chat'];

        return $chat->isOwner((int)$user);
    }
}
