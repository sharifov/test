<?php

namespace sales\rbac\rules\clientChat\couchNote;

use sales\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

/**
 * Class ClientChatCouchNoteInProgressRule
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
