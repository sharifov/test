<?php

namespace sales\model\leadRedial\listeners;

use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use modules\notification\src\abac\dto\NotificationAbacDto;
use modules\notification\src\abac\NotificationAbacObject;
use sales\model\leadRedial\entity\events\CallRedialAccessCreatedEvent;
use Yii;

class RedialCallAccessCreatedUserNotificationListener
{
    public function handle(CallRedialAccessCreatedEvent $event)
    {
        $notification = new Notifications();
        $notification->n_title = 'New General Line Call';
        $notification->n_type_id = Notifications::TYPE_SUCCESS;

        $notificationAbacDto = new NotificationAbacDto($notification);

        if (Yii::$app->abac->can($notificationAbacDto, NotificationAbacObject::OBJ_NOTIFICATION, NotificationAbacObject::ACTION_ACCESS)) {
            if ($ntf = Notifications::create($event->userId, 'New General Line Call', 'New General Line Call', Notifications::TYPE_SUCCESS, true)) {
                Notifications::publish('getNewNotification', ['user_id' => $event->userId], NotificationMessage::add($ntf));
            }
        }
    }
}
