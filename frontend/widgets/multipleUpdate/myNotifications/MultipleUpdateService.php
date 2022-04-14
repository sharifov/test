<?php

namespace frontend\widgets\multipleUpdate\myNotifications;

use common\models\Notifications;
use frontend\widgets\notification\NotificationCache;
use frontend\widgets\notification\NotificationMessage;

class MultipleUpdateService
{
    public function makeReadNotifications(array $ids, int $userId): void
    {
        if (Notifications::updateAll(['n_new' => false, 'n_read_dt' => date('Y-m-d H:i:s')], ['n_id' => $ids, 'n_user_id' => $userId])) {
            NotificationCache::invalidate($userId);
            $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::deleteBatch($ids, $userId) : [];
            Notifications::publish('getNewNotification', ['user_id' => $userId], $dataNotification);
        }
    }
}
