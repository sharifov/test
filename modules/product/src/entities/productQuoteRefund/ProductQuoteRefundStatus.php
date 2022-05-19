<?php

namespace modules\product\src\entities\productQuoteRefund;

use src\helpers\setting\SettingHelper;
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
    public const EXPIRED = 10;

    private const LIST = [
        self::NEW => 'New',
        self::PENDING => 'Pending',
        self::CONFIRMED => 'Confirmed',
        self::CANCELED => 'Canceled',
        self::COMPLETED => 'Completed',
        self::ERROR => 'Error',
        self::PROCESSING => 'Processing',
        self::IN_PROGRESS => 'In Progress',
        self::DECLINED => 'Declined',
        self::EXPIRED => 'Expired',
    ];

    private const CSS_CLASS_LIST = [
        self::NEW => 'primary',
        self::PENDING => 'warning',
        self::CONFIRMED => 'success',
        self::CANCELED => 'awake',
        self::COMPLETED => 'success',
        self::ERROR => 'danger',
        self::PROCESSING => 'info',
        self::IN_PROGRESS => 'info',
        self::DECLINED => 'awake',
        self::EXPIRED => 'danger',
    ];

    private const UNIQUE_KEY_LIST = [
        self::NEW => 'new',
        self::PENDING => 'pending',
        self::CONFIRMED => 'confirmed',
        self::CANCELED => 'canceled',
        self::COMPLETED => 'completed',
        self::ERROR => 'error',
        self::PROCESSING => 'processing',
        self::IN_PROGRESS => 'in_progress',
        self::DECLINED => 'declined',
        self::EXPIRED => 'expired',
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

    public static function getKeyById(int $id): ?string
    {
        return self::getKeyList()[$id] ?? null;
    }

    public static function getClientKeyStatusById(int $id): string
    {
        $key = self::getKeyById($id);
        $statusMap = SettingHelper::getProductQuoteRefundClientStatusMapping();
        return $statusMap[$key] ?? $key ?? '';
    }
}
