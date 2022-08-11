<?php

namespace src\entities\email\helpers;

/**
 * Class EmailStatus
 *
 * @package common\models\helpers
 */
class EmailStatus
{
    public const NEW     = 1;
    public const PENDING = 2;
    public const PROCESS = 3;
    public const CANCEL  = 4;
    public const DONE    = 5;
    public const ERROR   = 6;
    public const REVIEW  = 7;


    public static function getList(): array
    {
        return [
            self::NEW        => 'New',
            self::PENDING    => 'Pending',
            self::PROCESS    => 'Process',
            self::CANCEL     => 'Cancel',
            self::DONE       => 'Done',
            self::ERROR      => 'Error',
            self::REVIEW     => 'Review'
        ];
    }

    public static function getName(int $status): ?string
    {
        $map = self::getList();
        return $map[$status] ?? null;
    }

    public static function notError(int $status): bool
    {
        return $status !== self::ERROR;
    }

    public static function isDone(int $status): bool
    {
        return $status === self::DONE;
    }

    public static function isReview(int $status): bool
    {
        return $status === self::REVIEW;
    }

    public static function isErrorGroup(int $status): bool
    {
        return in_array($status, [self::ERROR, self::CANCEL]);
    }
}
