<?php

namespace sales\model\voip\phoneDevice\device;

use thamtech\uuid\helpers\UuidHelper;

class PhoneDeviceNameGenerator
{
    public static function generate(): string
    {
        return 'Device name #' . substr(str_replace('-', '', UuidHelper::uuid()), 0, 10);
    }
}
