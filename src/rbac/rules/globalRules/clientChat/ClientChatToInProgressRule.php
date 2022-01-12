<?php

namespace src\rbac\rules\globalRules\clientChat;

use src\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

/**
 * Class ClientChatToInProgressRule
 */
class ClientChatToInProgressRule extends Rule
{
    public $name = 'ClientChatToInProgressRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }
        $chat = $params['chat'];

        return $chat->isHold();
    }
}
