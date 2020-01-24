<?php

namespace modules\product\src\entities\productQuote;

use yii\bootstrap4\Html;

class ProductQuoteStatus
{
    public const PENDING         = 1;
    public const IN_PROGRESS     = 2;
    public const DONE            = 3;
    public const MODIFIED        = 4;
    public const DECLINED        = 5;
    public const CANCELED        = 6;

    public const STATUS_LIST        = [
        self::PENDING        => 'Pending',
        self::IN_PROGRESS    => 'In progress',
        self::DONE           => 'Done',
        self::MODIFIED       => 'Modified',
        self::DECLINED       => 'Declined',
        self::CANCELED       => 'Canceled',
    ];

    public const CLASS_LIST        = [
        self::PENDING        => 'warning',
        self::IN_PROGRESS    => 'info',
        self::DONE           => 'success',
        self::MODIFIED       => 'warning',
        self::DECLINED       => 'danger',
        self::CANCELED       => 'danger',
    ];

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value),
            ['class' => 'badge badge-' . self::getClassName($value)]
        );
    }

    public static function getName(?int $value)
    {
        return self::STATUS_LIST[$value] ?? 'Undefined';
    }

    private static function getClassName(?int $value): string
    {
        return self::CLASS_LIST[$value] ?? 'secondary';
    }
}
