<?php

namespace src\rbac\rules\clientChat\take;

use src\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatTakeNewRule extends Rule
{
    public $name = 'ClientChatTakeNewRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }

        /** @var ClientChat $chat */
        $chat = $params['chat'];

        return $chat->isNew();
    }
}
