<?php

namespace frontend\widgets\newWebPhone;

use thamtech\uuid\helpers\UuidHelper;

class DeviceHash
{
    public static function generate(): string
    {
        return UuidHelper::uuid();
    }

    public static function isValid(string $hash): bool
    {
        return UuidHelper::isValid($hash);
    }

    public static function getHashKey(int $userId): string
    {
        return 'deviceHash' . $userId;
    }
}
