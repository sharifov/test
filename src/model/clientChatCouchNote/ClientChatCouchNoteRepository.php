<?php

namespace src\model\clientChatCouchNote;

use src\model\clientChatCouchNote\entity\ClientChatCouchNote;

/**
 * Class ClientChatCouchNoteRepository
 */
class ClientChatCouchNoteRepository
{
    public function save(ClientChatCouchNote $clientChatCouchNote, int $code = 0): ClientChatCouchNote
    {
        if (!$clientChatCouchNote->save(false)) {
            throw new \RuntimeException('Client Chat Couch Note saving failed', $code);
        }
        return $clientChatCouchNote;
    }
}
