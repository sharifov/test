<?php

namespace sales\model\coupon\entity\coupon;

use yii\bootstrap4\Html;

class CouponType
{
    public const VOUCHER = 1;
    public const COUPON = 2;

    private const LIST = [
        self::VOUCHER => 'Voucher',
        self::COUPON => 'Coupon',
    ];

    private const CLASS_LIST = [
        self::VOUCHER => 'success',
        self::COUPON => 'info',
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
