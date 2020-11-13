<?php

namespace sales\rbac\rules\clientChat\transfer;

use sales\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatTransferHoldRule extends Rule
{
    public $name = 'ClientChatTransferHoldRule';

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
