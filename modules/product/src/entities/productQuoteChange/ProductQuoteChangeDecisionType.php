<?php

namespace modules\product\src\entities\productQuoteChange;

use yii\bootstrap4\Html;

class ProductQuoteChangeDecisionType
{
    public const CONFIRM = 1;
    public const MODIFY = 2;
    public const REFUND = 3;
    public const CREATE = 4;

    public const LIST = [
        self::CONFIRM => 'Confirm',
        self::MODIFY => 'Modify',
        self::REFUND => 'Refund',
        self::CREATE => 'Create',
    ];

    private const CLASS_LIST = [
        self::CONFIRM => 'info',
        self::MODIFY => 'warning',
        self::REFUND => 'info',
        self::CREATE => 'primary',
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

    public static function getName(?int $value): string
    {
        return self::LIST[$value] ?? 'Undefined';
    }

    private static function getClassName(?int $value): string
    {
        return self::CLASS_LIST[$value] ?? 'secondary';
    }
}
