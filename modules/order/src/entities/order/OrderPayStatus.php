<?php

namespace modules\order\src\entities\order;

use yii\bootstrap4\Html;

class OrderPayStatus
{
    public const NOT_PAID = 1;
    public const PAID = 2;
    public const PARTIAL_PAID = 3;

    public const LIST = [
        self::NOT_PAID => 'Not paid',
        self::PAID => 'Paid',
        self::PARTIAL_PAID => 'Partial paid',
    ];

    public const CLASS_LIST = [
        self::NOT_PAID => 'warning',
        self::PAID => 'success',
        self::PARTIAL_PAID => 'info',
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
