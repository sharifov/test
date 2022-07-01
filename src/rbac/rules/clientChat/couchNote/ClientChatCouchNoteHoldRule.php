<?php

namespace src\rbac\rules\clientChat\couchNote;

use src\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

/**
 * Class ClientChatCouchNoteHoldRule
 */
class ClientChatCouchNoteHoldRule extends Rule
{
    public $name = 'ClientChatCouchNoteHoldRule';

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
