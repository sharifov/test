<?php

namespace sales\rbac\rules\clientChat\hold;

use sales\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatHoldRule extends Rule
{
    public $name = 'ClientChatHoldRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }

        /** @var ClientChat $chat */
        $chat = $params['chat'];

        return $chat->isInProgress();
    }
}
