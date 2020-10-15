<?php

namespace sales\rbac\rules\clientChat\take;

use sales\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatTakeHoldRule extends Rule
{
    public $name = 'ClientChatTakeHoldRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }

        $user = (int)$user;

        /** @var ClientChat $chat */
        $chat = $params['chat'];

        return $chat->isHold() && !$chat->isOwner($user);
    }
}
