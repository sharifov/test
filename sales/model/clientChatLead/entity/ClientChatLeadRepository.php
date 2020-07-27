<?php

namespace sales\model\clientChatLead\entity;

use sales\repositories\NotFoundException;

class ClientChatLeadRepository
{
    public function find(int $chatId, int $leadId): ClientChatLead
    {
        if ($clientChatLead = ClientChatLead::findOne(['ccl_chat_id' => $chatId, 'ccl_lead_id' => $leadId])) {
            return $clientChatLead;
        }
        throw new NotFoundException('ClientChatLead is not found');
    }

    public function save(ClientChatLead $clientChatLead): void
    {
        if (!$clientChatLead->save(false)) {
            throw new \RuntimeException('Saving error');
        }
    }

    public function remove(ClientChatLead $clientChatLead): void
    {
        if (!$clientChatLead->delete()) {
            throw new \RuntimeException('Removing error');
        }
    }
}
