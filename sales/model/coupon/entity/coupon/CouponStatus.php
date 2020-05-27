<?php

namespace sales\model\coupon\entity\coupon;

use yii\bootstrap4\Html;

class CouponStatus
{
    public const NEW = 1;
    public const SEND = 2;
    public const USED = 3;
    public const CANCEL = 4;

    private const LIST = [
        self::NEW => 'New',
        self::SEND => 'Send',
        self::USED => 'Used',
        self::CANCEL => 'Cancel',
    ];

    private const CLASS_LIST = [
        self::NEW => 'info',
        self::SEND => 'warning',
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
