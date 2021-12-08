<?php

namespace sales\model\voip\phoneDevice\device;

use sales\helpers\UserCallIdentity;

class PhoneDeviceIdentityGenerator
{
    public static function generate(int $userId, string $postFix): string
    {
        return UserCallIdentity::getId($userId) . '_' . $postFix;
    }
}
