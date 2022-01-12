<?php

namespace sales\rbac\rules\clientChat\manage;

use sales\access\EmployeeGroupAccess;
use sales\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatManageGroupRule extends Rule
{
    public $name = 'ClientChatManageGroupRule';

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
