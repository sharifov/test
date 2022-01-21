<?php

namespace src\model\client\notifications\client\entity;

use src\repositories\NotFoundException;

class ClientNotificationRepository
{
    public function find(int $id): ClientNotification
    {
        $notification = ClientNotification::findOne($id);
        if ($notification) {
            return $notification;
        }
        throw new NotFoundException('Client Notification not found. ID: ' . $id);
    }

    public function save(ClientNotification $notification): void
    {
        if (!$notification->save(false)) {
            throw new \RuntimeException('Saving error.');
        }
    }

    public function remove(ClientNotification $notification): void
    {
        if (!$notification->delete()) {
            throw new \RuntimeException('Removing error.');
        }
    }
}
