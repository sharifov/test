<?php

namespace src\model\callLog\entity\callLog;

use yii\bootstrap4\Html;

class CallLogType
{
    public const OUT = 1;
    public const IN = 2;
    public const JOIN = 3;
    public const RETURN = 4;

    private const LIST = [
        self::OUT => 'Out',
        self::IN => 'In',
        self::JOIN => 'Join',
        self::RETURN => 'Return',
    ];

    private const CSS_CLASS_LIST = [
        self::OUT => 'warning',
        self::IN => 'info',
        self::JOIN => 'success',
        self::RETURN => 'primary',
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
