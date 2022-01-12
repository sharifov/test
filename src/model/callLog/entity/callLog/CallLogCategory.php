<?php

namespace src\model\callLog\entity\callLog;

use yii\bootstrap4\Html;

class CallLogCategory
{
    public const GENERAL_LINE = 1;
    public const DIRECT_CALL = 2;
    public const REDIRECT_CALL = 3;
    public const TRANSFER_CALL   = 4;
    public const CONFERENCE_CALL = 5;
    public const REDIAL_CALL = 6;
    public const COACH_CALL = 7;

    private const LIST = [
        self::GENERAL_LINE => 'General Line',
        self::DIRECT_CALL => 'Direct Call',
        self::REDIRECT_CALL => 'Redirect Call',
        self::TRANSFER_CALL => 'Transfer Call',
        self::CONFERENCE_CALL => 'Conference Call',
        self::REDIAL_CALL => 'Redial Call',
        self::COACH_CALL => 'Coach Call',
    ];

    private const CSS_CLASS_LIST = [
        self::GENERAL_LINE => 'info',
        self::DIRECT_CALL => 'warning',
        self::REDIRECT_CALL => 'primary',
        self::TRANSFER_CALL => 'light',
        self::CONFERENCE_CALL => 'dark',
        self::REDIAL_CALL => 'success',
        self::COACH_CALL => 'info',
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
