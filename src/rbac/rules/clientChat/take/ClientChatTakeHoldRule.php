<?php

namespace src\rbac\rules\clientChat\take;

use src\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatTakeHoldRule extends Rule
{
    public $name = 'ClientChatTakeHoldRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }

        /** @var ClientChat $chat */
        $chat = $params['chat'];

        return $chat->isHold();
    }
}
