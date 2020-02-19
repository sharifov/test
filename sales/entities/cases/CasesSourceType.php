<?php

namespace sales\entities\cases;

use yii\bootstrap4\Html;

class CasesSourceType
{
    public const CHAT = 1;
    public const CALL = 2;
    public const MAIL = 3;
    public const SMS = 4;
    public const REVIEW = 5;
    public const OTHER = 6;

    private const LIST = [
        self::CHAT => 'Chat',
        self::CALL => 'Call',
        self::MAIL => 'Mail',
        self::SMS => 'Sms',
        self::REVIEW => 'Review',
        self::OTHER => 'Other',
    ];

    private const CLASS_LIST = [
        self::CHAT => 'primary',
        self::CALL => 'success',
        self::MAIL => 'danger',
        self::SMS => 'light',
        self::REVIEW => 'warning',
        self::OTHER => 'info',
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
            ['class' => 'badge badge-' . self::getCssClass($value)]
        );
    }

    public static function getName(?int $value)
    {
        return self::LIST[$value] ?? 'Undefined';
    }

    private static function getCssClass(?int $value): string
    {
        return self::CLASS_LIST[$value] ?? 'secondary';
    }
}
