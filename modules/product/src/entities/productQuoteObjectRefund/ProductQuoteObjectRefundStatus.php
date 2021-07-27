<?php

namespace modules\product\src\entities\productQuoteObjectRefund;

use yii\helpers\Html;

class ProductQuoteObjectRefundStatus
{
    public const NEW = 1;
    public const PENDING = 2;
    public const ACCEPT = 3;
    public const CANCEL = 4;
    public const DONE = 5;
    public const ERROR = 6;

    private const LIST = [
        self::NEW => 'New',
        self::PENDING => 'Pending',
        self::ACCEPT => 'Accept',
        self::CANCEL => 'Cancel',
        self::DONE => 'Done',
        self::ERROR => 'Error',
    ];

    private const CSS_CLASS_LIST = [
        self::NEW => 'info',
        self::PENDING => 'warning',
        self::ACCEPT => 'success',
        self::CANCEL => 'danger',
        self::DONE => 'success',
        self::ERROR => 'danger',
    ];

    public static function getList()
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
