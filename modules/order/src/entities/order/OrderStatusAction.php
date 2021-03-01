<?php

namespace modules\order\src\entities\order;

use yii\bootstrap4\Html;

class OrderStatusAction
{
    public const JOB = 1;
    public const MANUAL = 2;

    private const LIST = [
        self::JOB => 'job',
        self::MANUAL => 'order/order-actions/cancel',
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
