<?php

namespace src\rbac\rules\clientChat\view;

use src\access\EmployeeGroupAccess;
use src\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatViewGroupRule extends Rule
{
    public $name = 'ClientChatViewGroupRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }

        /** @var ClientChat $chat */
        $chat = $params['chat'];

        if (!$chat->hasOwner()) {
            return false;
        }

        return EmployeeGroupAccess::isUserInCommonGroup($user, (int)$chat->cch_owner_user_id);
    }
}
