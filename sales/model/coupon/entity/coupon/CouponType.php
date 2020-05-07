<?php

namespace sales\model\coupon\entity\coupon;

use yii\bootstrap4\Html;

class CouponType
{
    public const COUPON = 1;
    public const VOUCHER = 2;

    private const LIST = [
        self::COUPON => 'Coupon',
        self::VOUCHER => 'Voucher',
    ];

    private const CLASS_LIST = [
        self::COUPON => 'info',
        self::VOUCHER => 'success',
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
