<?php

namespace src\helpers\user;

/**
 * Class GravatarHelper
 */
class GravatarHelper
{
    public static function getUrlByEmail(?string $email, int $s = 128, string $default = 'identicon'): string
    {
        if ($email) {
            return '//www.gravatar.com/avatar/' . md5(strtolower(trim($email))) . '?d=' . $default . '&s=' . $s;
        }
        return '//www.gravatar.com/avatar/?d=' . $default . '&s=60';
    }
}
