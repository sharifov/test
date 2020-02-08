<?php

namespace modules\qaTask\src\entities\qaTask;

use yii\bootstrap4\Html;

class QaTaskCreatedType
{
    public const JOB = 1;
    public const TRIGGER = 2;
    public const MANUALLY = 3;

    private const LIST = [
        self::JOB => 'Job',
        self::TRIGGER => 'Trigger',
        self::MANUALLY => 'Manually',
    ];

    private const CSS_CLASS_LIST = [
        self::JOB => 'success',
        self::TRIGGER => 'warning',
        self::MANUALLY => 'info',
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
