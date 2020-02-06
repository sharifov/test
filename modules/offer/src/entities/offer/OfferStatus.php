<?php

namespace modules\offer\src\entities\offer;

use yii\bootstrap4\Html;

class OfferStatus
{
    public const NEW = 1;
    public const SENT = 2;
    public const APPLY = 3;

    private const LIST = [
        self::NEW => 'New',
        self::SENT => 'Sent',
        self::APPLY => 'Apply',
    ];

    private const CLASS_LIST = [
        self::NEW => 'info',
        self::SENT => 'warning',
        self::APPLY => 'success',
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
