<?php

namespace src\model\clientChatRequest\repository;

use src\model\clientChatRequest\entity\ClientChatRequest;
use src\repositories\NotFoundException;

class ClientChatRequestRepository
{
    public function save(ClientChatRequest $model, int $attempts = 0): ClientChatRequest
    {
        try {
            $model->save(false);
        } catch (\Throwable $e) {
            if (strpos($e->getMessage(), "no partition of relation")) {
                $dates = ClientChatRequest::partitionDatesFrom(date_create_from_format('Y-m-d H:i:s', $model->ccr_created_dt));
                ClientChatRequest::createMonthlyPartition($dates[0], $dates[1]);
                if ($attempts > 0) {
                    throw new \RuntimeException("unable to create client_chat_request partition");
                }
                $this->save($model, ++$attempts);
            }
        }
        return $model;
    }

    public function find(int $id): ClientChatRequest
    {
        if (!$model = ClientChatRequest::findOne($id)) {
            throw new NotFoundException('Client Chat Request not found by ID: ' . $id);
        }
        return $model;
    }
}
