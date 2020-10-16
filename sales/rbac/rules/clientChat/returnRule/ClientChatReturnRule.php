<?php

namespace sales\rbac\rules\clientChat\returnRule;

use sales\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatReturnRule extends Rule
{
    public $name = 'ClientChatReturnRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }

        /** @var ClientChat $chat */
        $chat = $params['chat'];

        return $chat->isIdle();
    }
}
