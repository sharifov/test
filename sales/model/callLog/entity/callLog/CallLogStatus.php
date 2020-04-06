<?php

namespace sales\model\callLog\entity\callLog;

use yii\bootstrap4\Html;

class CallLogStatus
{
    public const COMPLETE = 5;
    public const BUSY = 6;
    public const NOT_ANSWERED = 7;
    public const FAILED = 8;
    public const CANCELED = 9;

    private const LIST = [
        self::COMPLETE => 'Complete',
        self::BUSY => 'Busy',
        self::NOT_ANSWERED => 'Not answered',
        self::FAILED => 'Failed',
        self::CANCELED => 'Canceled',
    ];

    private const CSS_CLASS_LIST = [
        self::COMPLETE => 'success',
        self::BUSY => 'warning',
        self::NOT_ANSWERED => 'info',
        self::FAILED => 'danger',
        self::CANCELED => 'dark',
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
