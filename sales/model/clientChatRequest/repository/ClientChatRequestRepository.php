<?php

namespace sales\model\clientChatRequest\repository;

use sales\model\clientChatRequest\entity\ClientChatRequest;

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
}
