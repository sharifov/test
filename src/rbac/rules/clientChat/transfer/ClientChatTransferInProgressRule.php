<?php

namespace src\rbac\rules\clientChat\transfer;

use src\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatTransferInProgressRule extends Rule
{
    public $name = 'ClientChatTransferInProgressRule';

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
