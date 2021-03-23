<?php

namespace sales\helpers\phone;

use sales\helpers\setting\SettingHelper;

class MaskPhoneHelper
{
    public static function masking(string $number): string
    {
        if (SettingHelper::clientDataPrivacyEnable()) {
            return substr($number, 0, 5) . str_repeat("*", strlen($number) - 8) . substr($number, - 3);
        }
        return $number;
    }
}
