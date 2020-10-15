<?php

namespace sales\model\clientChat\event;

use common\models\Notifications;
use frontend\widgets\clientChat\ClientChatAccessMessage;

/**
 * Class ClientChatSetStatusIdleListener
 */
class ClientChatSetStatusIdleListener
{
    public function handle(ClientChatSetStatusIdleEvent $event): void
    {
        try {
            Notifications::pub(
                ['chat-' . $event->clientChatId],
                'clientChatUpdateStatus',
                ['data' => ClientChatAccessMessage::chatIdle($event->clientChatId)]
            );
        } catch (\Throwable $throwable) {
            \Yii::error(
                $throwable,
                'ClientChatListener:ClientChatSetStatusIdleListener'
            );
        }
    }
}
