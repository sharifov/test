<?php

namespace sales\model\client\notifications\client\entity;

use yii\bootstrap4\Html;

class NotificationType
{
    public const PRODUCT_QUOTE_CHANGE = 1;

    private const LIST = [
        self::PRODUCT_QUOTE_CHANGE => 'ProductQuoteChange',
    ];

    private const CSS_CLASS_LIST = [
        self::PRODUCT_QUOTE_CHANGE => 'info',
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function getName(?int $value)
    {
        return self::LIST[$value] ?? 'Undefined';
    }

    private static function getCssClass(?int $value): string
    {
        return self::CSS_CLASS_LIST[$value] ?? 'secondary';
    }

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value),
            ['class' => 'badge badge-' . self::getCssClass($value)]
        );
    }
}
