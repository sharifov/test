<?php

namespace modules\fileStorage\src\entity\fileLog;

use yii\bootstrap4\Html;

class FileLogType
{
    public const VIEW = 1;
    public const DOWNLOAD = 2;

    private const LIST = [
        self::VIEW => 'View',
        self::DOWNLOAD => 'Download',
    ];

    private const CSS_CLASS_LIST = [
        self::VIEW => 'success',
        self::DOWNLOAD => 'warning',
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
