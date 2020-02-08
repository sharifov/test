<?php

namespace modules\qaTask\src\entities\qaTaskStatus;

use yii\bootstrap4\Html;

class QaTaskStatusAction
{
    public const TAKE = 1;
    public const RETURN = 2;
    public const ASSIGN = 3;
    public const CREATE = 4;

    private const LIST = [
        self::TAKE => 'Take',
        self::RETURN => 'Return',
        self::ASSIGN => 'Assign',
        self::CREATE => 'Create',
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value)
        );
    }

    public static function getName(?int $value)
    {
        return self::LIST[$value] ?? 'Undefined';
    }
}
