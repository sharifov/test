<?php

namespace src\entities\cases;

use yii\bootstrap4\Html;

class CasesSourceType
{
    public const CHAT = 1;
    public const CALL = 2;
    public const SMS = 3;
    public const MAIL = 4;
    public const REVIEW = 5;
    public const API = 6;
    public const CRM = 7;
    public const OTHER = 9;

    private const LIST = [
        self::CHAT => 'Chat',
        self::CALL => 'Call',
        self::SMS => 'Sms',
        self::MAIL => 'Mail',
        self::REVIEW => 'Review',
        self::API => 'Api',
        self::CRM => 'CRM',
        self::OTHER => 'Other',
    ];

    private const CLASS_LIST = [
        self::CHAT => 'primary',
        self::CALL => 'success',
        self::SMS => 'light',
        self::MAIL => 'danger',
        self::REVIEW => 'warning',
        self::API => 'info',
        self::CRM => 'info',
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
