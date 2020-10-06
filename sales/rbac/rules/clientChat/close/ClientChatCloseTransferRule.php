<?php

namespace sales\rbac\rules\clientChat\manage;

use sales\model\clientChat\entity\ClientChat;
use yii\rbac\Rule;

class ClientChatCloseTransferRule extends Rule
{
    public $name = 'ClientChatCloseTransferRule';

    public function execute($user, $item, $params): bool
    {
        if (!isset($params['chat']) || !$params['chat'] instanceof ClientChat) {
            return false;
        }

        if (!\Yii::$app->authManager->checkAccess($user, 'client-chat/manage')) {
            return false;
        }

        /** @var ClientChat $chat */
        $chat = $params['chat'];

        return $chat->isTransfer();
    }
}
