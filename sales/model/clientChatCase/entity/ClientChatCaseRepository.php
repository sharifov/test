<?php

namespace sales\model\clientChatCase\entity;

use sales\repositories\NotFoundException;

class ClientChatCaseRepository
{
    public function find(int $chatId, int $caseId): ClientChatCase
    {
        if ($clientChatCase = ClientChatCase::findOne(['cccs_chat_id' => $chatId, 'cccs_case_id' => $caseId])) {
            return $clientChatCase;
        }
        throw new NotFoundException('ClientChatCase is not found');
    }

    public function save(ClientChatCase $clientChatCase): void
    {
        if (!$clientChatCase->save(false)) {
            throw new \RuntimeException('Saving error');
        }
    }

    public function remove(ClientChatCase $clientChatCase): void
    {
        if (!$clientChatCase->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}
