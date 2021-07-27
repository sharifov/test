<?php

namespace modules\order\src\entities\orderRefund;

use yii\helpers\Html;

class OrderRefundClientStatus
{
    public const NEW = 1;
    public const PENDING = 2;
    public const ACCEPTED = 4;
    public const DECLINED = 5;
    public const DONE = 7;

    private const LIST = [
        self::NEW => 'New',
        self::PENDING => 'Pending',
        self::ACCEPTED => 'Accepted',
        self::DECLINED => 'Declined',
        self::DONE => 'Done',
    ];

    private const CSS_CLASS_LIST = [
        self::NEW => 'info',
        self::PENDING => 'warning',
        self::ACCEPTED => 'success',
        self::DECLINED => 'secondary',
        self::DONE => 'success',
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function getName(?int $value): string
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
