<?php

namespace sales\model\voip\phoneDevice\device;

use thamtech\uuid\helpers\UuidHelper;

class PhoneDeviceNameGenerator
{
    public static function generate(): string
    {
        return UuidHelper::uuid();
    }
}
