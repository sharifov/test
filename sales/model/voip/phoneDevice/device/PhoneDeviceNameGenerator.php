<?php

namespace sales\model\voip\phoneDevice\device;

class PhoneDeviceNameGenerator
{
    public static function generate(string $postFix): string
    {
        return 'Device name #' . $postFix;
    }
}
