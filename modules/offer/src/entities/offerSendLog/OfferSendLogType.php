<?php

namespace modules\offer\src\entities\offerSendLog;

use yii\bootstrap4\Html;

class OfferSendLogType
{
    public const EMAIL = 1;
    public const SMS = 2;

    private const LIST = [
        self::EMAIL => 'Email',
        self::SMS => 'Sms',
    ];

    private const CLASS_LIST = [
        self::EMAIL => 'info',
        self::SMS => 'warning',
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
