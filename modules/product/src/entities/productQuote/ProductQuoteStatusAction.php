<?php

namespace modules\product\src\entities\productQuote;

use yii\bootstrap4\Html;

class ProductQuoteStatusAction
{
    public const TEST = 1;

    private const LIST = [
        self::TEST => 'api/product-quote/action',
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value)
        );
    }

    public static function getName(?int $value)
    {
        return self::LIST[$value] ?? 'Undefined';
    }
}
