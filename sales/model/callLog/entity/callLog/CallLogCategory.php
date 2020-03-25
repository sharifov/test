<?php

namespace sales\model\callLog\entity\callLog;

use yii\bootstrap4\Html;

class CallLogCategory
{
    public const GL = 1;
    public const DL = 2;
    public const RC = 3;
    public const REDIAL = 4;

    private const LIST = [
        self::GL => 'GL',
        self::DL => 'DL',
        self::RC => 'RC',
        self::REDIAL => 'Redial',
    ];

    private const CSS_CLASS_LIST = [
        self::GL => 'info',
        self::DL => 'warning',
        self::RC => 'primary',
        self::REDIAL => 'success',
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
