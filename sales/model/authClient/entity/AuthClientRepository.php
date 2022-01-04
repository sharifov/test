<?php

namespace sales\model\authClient\entity;

use sales\repositories\NotFoundException;

class AuthClientRepository
{
    public function save(AuthClient $authClient): int
    {
        if (!$authClient->save()) {
            throw new \RuntimeException('AuthClient saving failed: ' . $authClient->getErrorSummary(true)[0]);
        }
        return $authClient->ac_id;
    }

    public function find(int $id): AuthClient
    {
        if (!$authClient = AuthClient::findOne(['ac_id' => $id])) {
            throw new NotFoundException('Auth Client not found');
        }
        return $authClient;
    }
}
