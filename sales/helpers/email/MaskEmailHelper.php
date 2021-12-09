<?php

namespace sales\helpers\email;

use sales\helpers\setting\SettingHelper;

class MaskEmailHelper
{
    public static function masking(?string $email, $forceShow = false): ?string
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && SettingHelper::clientDataPrivacyEnable() && !$forceShow) {
            return self::maskingPartial($email);
        }

        return $email;
    }

    public static function maskingPartial(?string $email): ?string
    {
        list($user, $domains) = explode('@', $email);

        $domains = explode('.', $domains);
        $user = strlen($user) >= 3 ? substr($user, 0, 3) : $user;
        $domainName = substr($domains['0'], -2);

        return $user . str_repeat('*', 5) . '@' . str_repeat('*', 2) . $domainName . '.' . $domains['1'];
    }
}
