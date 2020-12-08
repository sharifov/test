<?php

namespace sales\model\clientChatVisitor\repository;

use sales\model\clientChatVisitor\entity\ClientChatVisitor;
use sales\repositories\Repository;

class ClientChatVisitorRepository extends Repository
{
    public function create(int $cchId, int $visitorDataId, ?int $clientId): ClientChatVisitor
    {
        $clientChatVisitor = ClientChatVisitor::create($cchId, $visitorDataId, $clientId);
        $this->save($clientChatVisitor);
        return $clientChatVisitor;
    }

    public function save(ClientChatVisitor $clientChatVisitor): int
    {
        if (!$clientChatVisitor->save()) {
            throw new \RuntimeException($clientChatVisitor->getErrorSummary(false)[0]);
        }
        return $clientChatVisitor->ccv_id;
    }

    public function exists(int $cchId, int $cvdId): bool
    {
        return ClientChatVisitor::find()->byUniqueFields($cchId, $cvdId)->exists();
    }
}
