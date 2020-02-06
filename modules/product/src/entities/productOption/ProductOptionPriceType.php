<?php

namespace modules\product\src\entities\productOption;

use yii\bootstrap4\Html;

class ProductOptionPriceType
{
    public const AUTO = 1;
    public const MANUAL = 2;
    public const AUTO_MANUAL = 3;

    private const LIST = [
        self::AUTO => 'Auto',
        self::MANUAL => 'Manual',
        self::AUTO_MANUAL => 'Auto & Manual',
    ];

    public const CLASS_LIST = [
        self::AUTO => 'info',
        self::MANUAL => 'warning',
        self::AUTO_MANUAL => 'success',
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
