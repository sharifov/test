<?php

namespace sales\model\clientChatCouchNote;

use sales\model\clientChatCouchNote\entity\ClientChatCouchNote;
use sales\repositories\Repository;

/**
 * Class ClientChatCouchNoteRepository
 */
class ClientChatCouchNoteRepository extends Repository
{
    public function save(ClientChatCouchNote $clientChatCouchNote, int $code = 0): ClientChatCouchNote
    {
        if (!$clientChatCouchNote->save(false)) {
            throw new \RuntimeException('Client Chat Couch Note saving failed', $code);
        }
        return $clientChatCouchNote;
    }
}
