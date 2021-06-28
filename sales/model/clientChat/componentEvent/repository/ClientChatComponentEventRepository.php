<?php

namespace sales\model\clientChat\componentEvent\repository;

use sales\model\clientChat\componentEvent\entity\ClientChatComponentEvent;

class ClientChatComponentEventRepository
{
    public function save(ClientChatComponentEvent $componentEvent): int
    {
        if ($componentEvent->save()) {
            return $componentEvent->ccce_id;
        }
        throw new \RuntimeException($componentEvent->getErrorSummary(true)[0]);
    }
}
