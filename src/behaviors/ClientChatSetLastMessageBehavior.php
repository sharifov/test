<?php

namespace src\behaviors;

use src\model\clientChatLastMessage\ClientChatLastMessageRepository;
use src\model\clientChatMessage\entity\ClientChatMessage;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Class ClientChatLastMessageBehavior
 */
class ClientChatSetLastMessageBehavior extends Behavior
{
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'setLastMessage',
        ];
    }

    public function setLastMessage(): void
    {
        /** @var ClientChatMessage $clientChatMessage */
        $clientChatMessage = $this->owner;
        (new ClientChatLastMessageRepository())->createOrUpdateByMessage($clientChatMessage);
    }
}
