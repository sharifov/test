<?php

namespace sales\model\clientChatNote;

use sales\model\clientChatNote\entity\ClientChatNote;
use sales\repositories\NotFoundException;

/**
 * Class ClientChatNoteRepository
 */
class ClientChatNoteRepository
{
    public function save(ClientChatNote $clientChatNote): ClientChatNote
    {
        if (!$clientChatNote->save(false)) {
            throw new \RuntimeException('Client Chat Note saving failed');
        }
        return $clientChatNote;
    }

    public function markDeleted(ClientChatNote $clientChatNote): ClientChatNote
    {
        $clientChatNote->ccn_deleted = true;
        $this->save($clientChatNote);
        return $clientChatNote;
    }

    public function toggleDeleted(ClientChatNote $clientChatNote): void
    {
        $clientChatNote->ccn_deleted = $clientChatNote->ccn_deleted ? false : true ;
        $this->save($clientChatNote);
    }

    public function findById(int $id): ClientChatNote
    {
        if ($clientChat = ClientChatNote::findOne($id)) {
            return $clientChat;
        }
        throw new NotFoundException('Client chat note is not found');
    }
}
