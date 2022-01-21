<?php

namespace src\helpers\phone;

use src\helpers\setting\SettingHelper;

class MaskPhoneHelper
{
    public static function masking(?string $number, $forceShow = false): ?string
    {
        if (!$number) {
            return null;
        }
        if (SettingHelper::clientDataPrivacyEnable()  && !$forceShow && strlen($number) > 8) {
            return self::maskingPartial($number);
        }
        return $number;
    }

    /**
     * @param string $number
     * @return string
     */
    public static function maskingPartial(string $number): string
    {
        return substr($number, 0, 5) . str_repeat("*", strlen($number) - 8) . substr($number, - 3);
    }
}
