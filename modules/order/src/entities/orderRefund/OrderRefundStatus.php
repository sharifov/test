<?php

namespace modules\order\src\entities\orderRefund;

use yii\helpers\Html;

class OrderRefundStatus
{
    public const NEW = 1;
    public const PENDING = 2;
    public const PROCESSING = 3;
    public const ACCEPT = 4;
    public const DECLINE = 5;
    public const DONE = 6;
    public const CANCEL = 7;
    public const ERROR = 9;

    private const LIST = [
        self::NEW => 'New',
        self::PENDING => 'Pending',
        self::PROCESSING => 'Processing',
        self::ACCEPT => 'Accept',
        self::DONE => 'Done',
        self::DECLINE => 'Decline',
        self::CANCEL => 'Cancel',
        self::ERROR => 'Error',
    ];

    private const CSS_CLASS_LIST = [
        self::NEW => 'info',
        self::PENDING => 'info',
        self::PROCESSING => 'warning',
        self::ACCEPT => 'success',
        self::DONE => 'success',
        self::DECLINE => 'secondary',
        self::CANCEL => 'danger',
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
