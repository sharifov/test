<?php

namespace sales\model\userData\entity;

use yii\bootstrap4\Html;

class UserDataKey
{
    public const GROSS_PROFIT = 1;
    public const CONVERSION_PERCENT = 2;

    private const LIST = [
        self::GROSS_PROFIT => 'Gross Profit',
        self::CONVERSION_PERCENT => 'Conversion percent',
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function getNameById(int $id): ?string
    {
        return self::LIST[$id] ?? null;
    }

    public static function asFormat(?int $value): string
    {
        return self::getNameById($value);
//        return Html::tag(
//            'span',
//            self::getNameById($value),
//            ['class' => 'badge badge-default']
//        );
    }
}
