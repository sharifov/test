<?php

namespace sales\helpers\email;

use sales\helpers\setting\SettingHelper;

class MaskEmailHelper
{
    public static function masking(string $email): string
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && SettingHelper::clientDataPrivacyEnable()) {
            list($first, $last) = explode('@', $email);
            $first = str_replace(substr($first, '3'), str_repeat('*', strlen($first) - 3), $first);
            $last = explode('.', $last);
            $last_domain = str_replace(substr($last['0'], '0', strlen($last['0']) - 2), str_repeat('*', strlen($last['0']) - 2), $last['0']);
            return $first . '@' . $last_domain . '.' . $last['1'];
        }

        return $email;
    }
}
