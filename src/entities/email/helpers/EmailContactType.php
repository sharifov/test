<?php

namespace src\entities\email\helpers;

use src\entities\email\Email;

/**
 * Class EmailContactType
 *
 * @package common\models\helpers
 */
class EmailContactType
{
    public const FROM  = 1;
    public const TO    = 2;
    public const CC    = 3;
    public const BCC   = 4;

    public static function getList(): array
    {
        return [
            self::FROM     => 'From',
            self::TO       => 'To',
            self::CC       => 'Cc',
            self::BCC      => 'Bcc',
        ];
    }

    public static function getName(int $type): ?string
    {
        $map = self::getList();
        return $map[$type] ?? null;
    }

    public static function isFrom(int $type): bool
    {
        return $type === self::FROM;
    }

    public static function isTo(int $type): bool
    {
        return $type === self::TO;
    }

    public static function isCc(int $type): bool
    {
        return $type === self::CC;
    }

    public static function isBcc(int $type): bool
    {
        return $type === self::BCC;
    }
}
