<?php

namespace modules\email\src\helpers;

class MailHelper
{
    public static function isExistInBlackList($email): bool
    {
        return in_array($email, self::getBlackListFromAddress(), true);
    }

    private static function getBlackListFromAddress(): array
    {
        return [
            'mailer-daemon@googlemail.com',
        ];
    }

    public static function cleanText($text): string
    {
        return trim(iconv(mb_detect_encoding($text, mb_detect_order(), true), 'UTF-8//IGNORE', $text));
    }
}
