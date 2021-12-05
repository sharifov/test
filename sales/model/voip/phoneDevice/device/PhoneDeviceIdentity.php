<?php

namespace sales\model\voip\phoneDevice\device;

use sales\helpers\UserCallIdentity;

class PhoneDeviceIdentity
{
    public static function getId(int $userId, string $hash): string
    {
        return UserCallIdentity::getClientId($userId) . '_' . substr($hash, 0, 4);
    }
}
