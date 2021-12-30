<?php

namespace sales\model\authClient\entity;

class AuthClientRepository
{
    public function save(AuthClient $authClient): int
    {
        if (!$authClient->save()) {
            throw new \RuntimeException('AuthClient saving failed: ' . $authClient->getErrorSummary(true)[0]);
        }
        return $authClient->ac_id;
    }
}
