<?php

namespace src\rbac\rules\clientChat\close;

use src\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatCloseInProgressRule extends Rule
{
    public $name = 'ClientChatCloseInProgressRule';

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
