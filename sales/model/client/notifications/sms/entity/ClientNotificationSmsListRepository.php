<?php

namespace sales\model\client\notifications\sms\entity;

use sales\repositories\NotFoundException;

class ClientNotificationSmsListRepository
{
    public function find(int $id): ClientNotificationSmsList
    {
        $notification = ClientNotificationSmsList::findOne($id);
        if ($notification) {
            return $notification;
        }
        throw new NotFoundException('Client sms notification not found. ID: ' . $id);
    }

    public function findBySid(string $smsSid): ClientNotificationSmsList
    {
        $notification = ClientNotificationSmsList::find()->andWhere(['cnsl_sms_sid' => $smsSid])->one();
        if ($notification) {
            return $notification;
        }
        throw new NotFoundException('Client sms notification not found. SmsSid: ' . $smsSid);
    }

    public function save(ClientNotificationSmsList $notification): void
    {
        if (!$notification->save(false)) {
            throw new \RuntimeException('Saving error.');
        }
    }

    public function remove(ClientNotificationSmsList $notification): void
    {
        if (!$notification->delete()) {
            throw new \RuntimeException('Removing error.');
        }
    }
}
