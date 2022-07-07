<?php

namespace src\rbac\rules\clientChat\couchNote;

use src\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

/**
 * Class ClientChatCouchNoteIdleRule
 */
class ClientChatCouchNoteIdleRule extends Rule
{
    public $name = 'ClientChatCouchNoteIdleRule';

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
