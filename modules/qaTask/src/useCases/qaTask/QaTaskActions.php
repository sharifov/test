<?php

namespace modules\qaTask\src\useCases\qaTask;

use yii\bootstrap4\Html;

class QaTaskActions
{
    public const TAKE = 1;
    public const TAKE_OVER = 2;
    public const CANCEL = 3;
    public const CLOSE = 4;
    public const ESCALATE = 5;
    public const RETURN = 6;

    private const LIST = [
        self::TAKE => 'Take',
        self::TAKE_OVER => 'Take Over',
        self::CANCEL => 'Cancel',
        self::CLOSE => 'Close',
        self::ESCALATE => 'Escalate',
        self::RETURN => 'Return',
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
