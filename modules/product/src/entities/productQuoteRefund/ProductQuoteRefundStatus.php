<?php

namespace modules\product\src\entities\productQuoteRefund;

use yii\helpers\Html;

class ProductQuoteRefundStatus
{
    public const NEW = 1;
    public const PENDING = 2;
    public const CONFIRMED = 3;
    public const CANCELED = 4;
    public const COMPLETED = 5;
    public const ERROR = 6;
    public const PROCESSING = 7;
    public const IN_PROGRESS = 8;
    public const DECLINED = 9;

    private const LIST = [
        self::NEW => 'New',
        self::PENDING => 'Pending',
        self::CONFIRMED => 'Confirmed',
        self::CANCELED => 'Canceled',
        self::COMPLETED => 'Done',
        self::ERROR => 'Error',
        self::PROCESSING => 'Processing',
        self::IN_PROGRESS => 'In Progress',
        self::DECLINED => 'Declined'
    ];

    private const CSS_CLASS_LIST = [
        self::NEW => 'info',
        self::PENDING => 'warning',
        self::CONFIRMED => 'success',
        self::CANCELED => 'danger',
        self::COMPLETED => 'success',
        self::ERROR => 'danger',
        self::PROCESSING => 'info',
        self::IN_PROGRESS => 'info',
        self::DECLINED => 'danger'
    ];

    private const UNIQUE_KEY_LIST = [
        self::NEW => 'new',
        self::PENDING => 'pending',
        self::CONFIRMED => 'confirmed',
        self::CANCELED => 'canceled',
        self::COMPLETED => 'done',
        self::ERROR => 'error',
        self::PROCESSING => 'processing',
        self::IN_PROGRESS => 'in_progress',
        self::DECLINED => 'declined'
    ];

    private const BO_STATUSES_LIST = [
        self::NEW => '',
        self::PENDING => '',
        self::CONFIRMED => 'requested',
        self::CANCELED => '',
        self::COMPLETED => 'refunded',
        self::ERROR => '',
        self::PROCESSING => 'processing',
        self::IN_PROGRESS => 'requested',
        self::DECLINED => 'canceled'
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

    public static function getKeyList(): array
    {
        return self::UNIQUE_KEY_LIST;
    }

    public static function getBoStatusesList(): array
    {
        return self::BO_STATUSES_LIST;
    }

    public static function getKeyById(int $id): ?string
    {
        return self::getKeyList()[$id] ?? null;
    }

    public static function getBoKeyStatusById(int $id): ?string
    {
        return self::getBoStatusesList()[$id] ?? null;
    }
}
