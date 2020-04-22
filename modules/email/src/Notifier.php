<?php

namespace modules\email\src;

use common\models\Employee;
use common\models\Notifications;
use common\models\UserProjectParams;
use frontend\widgets\notification\NotificationMessage;
use Yii;

class Notifier
{
    public function notifyToEmails(array $emailsTo): void
    {
        if (!$emailsTo) {
            return;
        }

        $this->notifyToUsers($this->getUsersIds($emailsTo));
    }

    public function notifyToUsers(array $usersIds): void
    {
        if (!$usersIds) {
            return;
        }

        foreach ($usersIds as $userId) {
            if ($ntf = Notifications::create($userId, 'New Emails received', 'New Emails received. Check your inbox.', Notifications::TYPE_INFO, true)) {
                // Notifications::socket($user_id, null, 'getNewNotification', [], true);
                $dataNotification = (Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
                Notifications::sendSocket('getNewNotification', ['user_id' => $userId], $dataNotification);
            }
        }
    }

    private function getUsersIds(array $emailsTo): array
    {
        $usersFromParams = UserProjectParams::find()->select(['upp_user_id'])->byEmail($emailsTo, false)->indexBy('upp_user_id')->column();
        $usersFromEmployees = Employee::find()->where(['email' => $emailsTo])->indexBy('email')->column();
        return array_unique(array_merge($usersFromParams, $usersFromEmployees));
    }
}
