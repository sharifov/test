<?php

namespace src\model\client\notifications\client\entity;

use yii\bootstrap4\Html;

class CommunicationType
{
    public const PHONE = 1;
    public const SMS = 2;
    public const EMAIL = 3;

    private const LIST = [
        self::PHONE => 'Phone',
        self::SMS => 'Sms',
        self::EMAIL => 'Email',
    ];

    private const CSS_CLASS_LIST = [
        self::PHONE => 'success',
        self::SMS => 'warning',
        self::EMAIL => 'info',
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
