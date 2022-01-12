<?php

namespace src\model\client\notifications\phone\entity;

use src\repositories\NotFoundException;

class ClientNotificationPhoneListRepository
{
    public function find(int $id): ClientNotificationPhoneList
    {
        $notification = ClientNotificationPhoneList::findOne($id);
        if ($notification) {
            return $notification;
        }
        throw new NotFoundException('Client phone notification not found. ID: ' . $id);
    }

    public function findBySid(string $callSid): ClientNotificationPhoneList
    {
        $notification = ClientNotificationPhoneList::find()->andWhere(['cnfl_call_sid' => $callSid])->one();
        if ($notification) {
            return $notification;
        }
        throw new NotFoundException('Client phone notification not found. CallSid: ' . $callSid);
    }

    public function save(ClientNotificationPhoneList $notification): void
    {
        if (!$notification->save(false)) {
            throw new \RuntimeException('Saving error.');
        }
    }

    public function remove(ClientNotificationPhoneList $notification): void
    {
        if (!$notification->delete()) {
            throw new \RuntimeException('Removing error.');
        }
    }
}
