<?php

namespace src\entities\email\helpers;

use src\entities\email\Email;

/**
 * Class EmailFilterType
 *
 * @package common\models\helpers
 */
class EmailFilterType
{
    public const ALL        = 1;
    public const INBOX      = 2;
    public const OUTBOX     = 3;
    public const DRAFT      = 4;
    public const TRASH      = 5;

    public static function getList(): array
    {
        return [
            self::ALL    => 'ALL',
            self::INBOX   => 'INBOX',
            self::OUTBOX    => 'OUTBOX',
            self::DRAFT    => 'DRAFT',
            self::TRASH    => 'TRASH',
        ];
    }

    public static function getName(int $type): ?string
    {
        $map = self::getList();
        return $map[$type] ?? null;
    }

    public static function isAll(int $type): bool
    {
        return $type === self::ALL;
    }

    public static function isInbox(int $type): bool
    {
        return $type === self::INBOX;
    }

    public static function isOutbox(int $type): bool
    {
        return $type === self::OUTBOX;
    }

    public static function isDraft(int $type): bool
    {
        return $type === self::DRAFT;
    }

    public static function isTrash(int $type): bool
    {
        return $type === self::TRASH;
    }
}
