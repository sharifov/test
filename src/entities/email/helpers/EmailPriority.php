<?php

namespace src\entities\email\helpers;

use src\entities\email\Email;

/**
 * Class EmailPriority
 *
 * @package common\models\helpers
 */
class EmailPriority
{
    public const LOW    = 1;
    public const NORMAL = 2;
    public const HIGH   = 3;

    public static function getList(): array
    {
        return [
            self::LOW     => 'Low',
            self::NORMAL  => 'Normal',
            self::HIGH    => 'High',
        ];
    }

    public static function getName(int $priority): ?string
    {
        $map = self::getList();
        return $map[$priority] ?? null;
    }

    public static function isLow(int $priority): bool
    {
        return $priority === self::LOW;
    }

    public static function isNormal(int $priority): bool
    {
        return $priority === self::NORMAL;
    }

    public static function isHigh(int $priority): bool
    {
        return $priority === self::HIGH;
    }
}
