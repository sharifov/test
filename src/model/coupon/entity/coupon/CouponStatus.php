<?php

namespace src\model\coupon\entity\coupon;

use yii\bootstrap4\Html;

class CouponStatus
{
    public const NEW = 1;
    public const SENT = 2;
    public const USED = 3;
    public const CANCEL = 4;
    public const IN_PROGRESS = 5;

    public const VALID_STATUS_LIST = [
        self::NEW,
        self::SENT,
        self::IN_PROGRESS,
    ];

    private const LIST = [
        self::NEW => 'New',
        self::SENT => 'Sent',
        self::USED => 'Used',
        self::CANCEL => 'Cancel',
        self::IN_PROGRESS => 'In Progress',
    ];

    private const CLASS_LIST = [
        self::NEW => 'info',
        self::SENT => 'warning',
        self::IN_PROGRESS => 'warning',
        self::USED => 'success',
        self::CANCEL => 'success',
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value),
            ['class' => 'badge badge-' . self::getClassName($value)]
        );
    }

    public static function getName(?int $value)
    {
        return self::LIST[$value] ?? 'Undefined';
    }

    private static function getClassName(?int $value): string
    {
        return self::CLASS_LIST[$value] ?? 'secondary';
    }
}
