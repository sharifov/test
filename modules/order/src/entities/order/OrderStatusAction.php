<?php

namespace modules\order\src\entities\order;

use yii\bootstrap4\Html;

class OrderStatusAction
{
    public const JOB = 1;
    public const MANUAL = 2;
    public const MULTIPLE_UPDATE = 3;
    public const API = 4;
    public const AUTO_PROCESSING = 5;
    public const CANCEL_FLOW = 6;

    private const LIST = [
        self::JOB => 'job',
        self::MANUAL => 'order/order-actions/cancel',
        self::MULTIPLE_UPDATE => 'multiple-update',
        self::API => 'api',
        self::AUTO_PROCESSING => 'auto processing',
        self::CANCEL_FLOW => 'cancel flow',
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
