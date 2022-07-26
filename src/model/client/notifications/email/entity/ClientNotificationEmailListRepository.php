<?php

namespace src\model\client\notifications\email\entity;

use src\repositories\NotFoundException;

class ClientNotificationEmailListRepository
{
    public function find(int $id): ClientNotificationEmailList
    {
        $notification = ClientNotificationEmailList::findOne($id);
        if ($notification) {
            return $notification;
        }
        throw new NotFoundException('Client email notification not found. ID: ' . $id);
    }

    public function save(ClientNotificationEmailList $notification): void
    {
        if (!$notification->save(false)) {
            throw new \RuntimeException('Saving error.');
        }
    }

    public function remove(ClientNotificationEmailList $notification): void
    {
        if (!$notification->delete()) {
            throw new \RuntimeException('Removing error.');
        }
    }
}
