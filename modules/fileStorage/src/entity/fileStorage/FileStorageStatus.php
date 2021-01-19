<?php

namespace modules\fileStorage\src\entity\fileStorage;

use yii\bootstrap4\Html;

class FileStorageStatus
{
    public const PENDING = 1;
    public const FAILED = 2;
    public const UPLOADED = 3;

    private const LIST = [
        self::PENDING => 'Pending',
        self::FAILED => 'Failed',
        self::UPLOADED => 'Uploaded',
    ];

    private const CSS_CLASS_LIST = [
        self::PENDING => 'warning',
        self::FAILED => 'danger',
        self::UPLOADED => 'success',
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
