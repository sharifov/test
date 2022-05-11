<?php

namespace modules\product\src\entities\productQuoteData;

use yii\bootstrap4\Html;

class ProductQuoteDataKey
{
    public const RECOMMENDED = 1;
    public const CONFIRMED = 2;

    public const VALUES = [
        self::RECOMMENDED => '1',
        self::CONFIRMED => '1',
    ];

    private const LIST_NAME = [
        self::RECOMMENDED => 'Recommended',
        self::CONFIRMED => 'Confirmed',
    ];

    private const LIST_CLASS = [
        self::RECOMMENDED => 'info',
        self::CONFIRMED => 'success',
    ];

    public static function getList(): array
    {
        return self::LIST_NAME;
    }

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value),
            ['class' => 'badge badge-' . self::getClassName($value)]
        );
    }

    public static function getName(?int $value): ?string
    {
        return self::getList()[$value] ?? null;
    }

    private static function getClassName(?int $value): string
    {
        return self::LIST_CLASS[$value] ?? 'secondary';
    }

    public static function getValueByKey(int $key): string
    {
        if (isset(self::VALUES[$key])) {
            return self::VALUES[$key];
        }
        throw new \RuntimeException('Product Quote Data value is not found by key: ' . $key);
    }
}
