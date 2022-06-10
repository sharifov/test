<?php

namespace src\entities\email\helpers;

/**
 * Class EmailType
 *
 * @package common\models\helpers
 */
class EmailType
{
    public const DRAFT     = 0;
    public const OUTBOX    = 1;
    public const INBOX     = 2;


    public static function getList(): array
    {
        return [
            self::DRAFT    => 'Draft',
            self::OUTBOX   => 'Outbox',
            self::INBOX    => 'Inbox',
        ];
    }

    public static function getName(int $type): ?string
    {
        $map = self::getList();
        return $map[$type] ?? null;
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
}
