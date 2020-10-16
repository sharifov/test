<?php

namespace sales\rbac\rules\clientChat\take;

use sales\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatTakeIdleRule extends Rule
{
    public $name = 'ClientChatTakeIdleRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }

        $user = (int)$user;

        /** @var ClientChat $chat */
        $chat = $params['chat'];

        return $chat->isIdle() && !$chat->isOwner($user);
    }
}
