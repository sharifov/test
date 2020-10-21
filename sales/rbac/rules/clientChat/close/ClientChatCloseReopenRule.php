<?php


namespace sales\rbac\rules\clientChat\close;

use sales\model\clientChat\entity\ClientChat;
use sales\model\clientChat\entity\ClientChatQuery;
use yii\rbac\Rule;

class ClientChatCloseReopenRule extends Rule
{
    public $name = 'ClientChatCloseReopenRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }

        /** @var ClientChat $chat */
        $chat = $params['chat'];

        $clientChat = ClientChatQuery::lastSameChat($chat->cch_rid);

        return $chat->isClosed() && $clientChat->isClosed() && ($clientChat->cch_id === $chat->cch_id);
    }
}