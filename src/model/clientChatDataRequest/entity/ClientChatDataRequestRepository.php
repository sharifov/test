<?php

namespace src\model\clientChatDataRequest\entity;

use src\repositories\NotFoundException;

class ClientChatDataRequestRepository
{
    public function save(ClientChatDataRequest $clientChatDataRequest): int
    {
        if (!$clientChatDataRequest->save()) {
            throw new \RuntimeException('Client Chat Data Request saving failed: ' . $clientChatDataRequest->getErrorSummary(true)[0]);
        }
        return $clientChatDataRequest->ccdr_id;
    }

    public function find(int $id): ClientChatDataRequest
    {
        if (!$clientChatDataRequest = ClientChatDataRequest::findOne($id)) {
            throw new NotFoundException('Client Chat Data Request not found by id: ' . $id);
        }
        return $clientChatDataRequest;
    }
}
