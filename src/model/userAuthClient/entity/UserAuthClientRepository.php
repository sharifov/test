<?php

namespace src\model\userAuthClient\entity;

use src\repositories\NotFoundException;

class UserAuthClientRepository
{
    public function save(UserAuthClient $authClient): int
    {
        if (!$authClient->save()) {
            throw new \RuntimeException('UserAuthClient saving failed: ' . $authClient->getErrorSummary(true)[0]);
        }
        return $authClient->uac_id;
    }

    public function find(int $id): UserAuthClient
    {
        if (!$authClient = UserAuthClient::findOne(['uac_id' => $id])) {
            throw new NotFoundException('Auth Client not found');
        }
        return $authClient;
    }
}
