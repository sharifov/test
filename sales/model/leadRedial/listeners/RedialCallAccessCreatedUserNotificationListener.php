<?php

namespace sales\model\leadRedial\listeners;

use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use sales\model\leadRedial\entity\events\CallRedialAccessCreatedEvent;

class RedialCallAccessCreatedUserNotificationListener
{
    public function handle(CallRedialAccessCreatedEvent $event)
    {
        if ($ntf = Notifications::create($event->userId, 'New General Line Call', 'New General Line Call', Notifications::TYPE_SUCCESS, true)) {
            Notifications::publish('getNewNotification', ['user_id' => $event->userId], NotificationMessage::add($ntf));
        }
    }
}
