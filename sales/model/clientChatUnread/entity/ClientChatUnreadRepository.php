<?php

namespace sales\model\clientChatUnread\entity;

use sales\repositories\NotFoundException;

class ClientChatUnreadRepository
{
    public function find(int $id): ClientChatUnread
    {
        if ($unread = ClientChatUnread::findOne($id)) {
            return $unread;
        }
        throw new NotFoundException('Client chat unread message is not found');
    }

    public function get(int $id): ?ClientChatUnread
    {
        if ($unread = ClientChatUnread::findOne($id)) {
            return $unread;
        }
        return null;
    }

    public function save(ClientChatUnread $unread): void
    {
        if (!$unread->save()) {
            throw new \RuntimeException($unread->getErrorSummary(false)[0]);
        }
    }

    public function remove(ClientChatUnread $unread): void
    {
        if (!$unread->delete()) {
            throw new \RuntimeException('Removing ClientChatUnread error');
        }
    }
}
