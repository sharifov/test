<?php

namespace sales\rbac\rules\clientChat\manage;

use sales\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatManageEmptyRule extends Rule
{
    public $name = 'ClientChatManageEmptyRule';

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
