<?php

namespace sales\model\voip\phoneDevice\device;

use sales\helpers\UserCallIdentity;

class PhoneDeviceIdentity
{
    public static function getClientId(int $userId, string $hash): string
    {
        return UserCallIdentity::getClientId($userId) . '_' . self::processHash($hash);
    }

    public static function getId(int $userId, string $hash): string
    {
        return UserCallIdentity::getId($userId) . '_' . self::processHash($hash);
    }

    private static function processHash(string $hash): string
    {
        $hash = str_replace(['-', 1, 2, 3, 4, 5, 6, 7, 8, 9, 0], [''], $hash);
        return substr($hash, 0, 5);
    }
}
