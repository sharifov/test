<?php

namespace modules\fileStorage\src\services;

use modules\fileStorage\src\entity\fileStorage\Uid;

class PathGenerator
{
    public static function byClient(int $clientId, string $projectKey, string $originalName, Uid $uid): string
    {
        $chunks = [
            $projectKey,
            'client',
            $clientId,
            date('Y'),
            date('m'),
            $uid->getValue(),
            $originalName
        ];
        return implode('/', $chunks);
    }

    public static function byClientAndUid(int $clientId, string $projectKey, string $originalName, string $uid): string
    {
        return implode('/', [
            $projectKey,
            'client',
            $clientId,
            date('Y'),
            date('m'),
            $uid,
            $originalName
        ]);
    }

    public static function byOrder(int $orderId, string $projectKey, string $originalName, string $uid): string
    {
        return implode('/', [
            $projectKey,
            'order',
            $orderId,
            date('Y'),
            date('m'),
            $uid,
            $originalName
        ]);
    }
}
