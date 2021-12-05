<?php

namespace sales\model\voip\phoneDevice;

use yii\bootstrap4\Html;

class PhoneDeviceLogLevel
{
    public const ERROR = 4;

    private const LIST = [
        self::ERROR => 'Error'
    ];

    private const CLASS_LIST = [
        self::ERROR => 'danger',
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function isExist(int $typeId): bool
    {
        return isset(self::getList()[$typeId]);
    }

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value),
            ['class' => 'badge badge-' . self::getCssClass($value)]
        );
    }

    public static function getName(?int $value)
    {
        return self::LIST[$value] ?? 'Undefined';
    }

    private static function getCssClass(?int $value): string
    {
        return self::CLASS_LIST[$value] ?? 'secondary';
    }
}
