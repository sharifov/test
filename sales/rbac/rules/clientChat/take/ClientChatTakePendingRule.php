<?php

namespace sales\rbac\rules\clientChat\take;

use sales\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatTakePendingRule extends Rule
{
    public $name = 'ClientChatTakePendingRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }

        /** @var ClientChat $chat */
        $chat = $params['chat'];

        return $chat->isPending();
    }
}
