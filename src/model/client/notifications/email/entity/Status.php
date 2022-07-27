<?php

namespace src\model\client\notifications\email\entity;

use yii\bootstrap4\Html;

class Status
{
    public const NEW = 1;
    public const PROCESSING = 2;
    public const CANCELED = 3;
    public const ERROR = 4;
    public const DONE = 5;

    private const LIST = [
        self::NEW => 'New',
        self::PROCESSING => 'Processing',
        self::CANCELED => 'Canceled',
        self::ERROR => 'Error',
        self::DONE => 'Done',
    ];

    private const CSS_CLASS_LIST = [
        self::NEW => 'info',
        self::PROCESSING => 'danger',
        self::CANCELED => 'warning',
        self::ERROR => 'secondary',
        self::DONE => 'success',
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
