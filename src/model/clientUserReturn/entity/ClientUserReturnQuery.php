<?php

namespace src\model\clientUserReturn\entity;

class ClientUserReturnQuery
{
    public static function exists(int $clientId, int $userId): bool
    {
        return ClientUserReturn::find()->byClient($clientId)->byUser($userId)->exists();
    }
}
