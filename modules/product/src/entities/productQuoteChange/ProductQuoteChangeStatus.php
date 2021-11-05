<?php

namespace modules\product\src\entities\productQuoteChange;

use sales\helpers\setting\SettingHelper;
use yii\bootstrap4\Html;

class ProductQuoteChangeStatus
{
    public const NEW = 1;
    public const PENDING = 2;
    public const CONFIRMED = 10;
    public const IN_PROGRESS = 3;
    public const PROCESSING = 8;
    public const COMPLETED = 4;
    public const CANCELED = 5;
    public const ERROR = 6;
    public const DECLINED = 7;

    public const LIST = [
        self::NEW => 'New',
        self::PENDING => 'Pending',
        self::IN_PROGRESS => 'In progress',
        self::COMPLETED => 'Completed',
        self::CANCELED => 'Cancelled',
        self::ERROR => 'Error',
        self::DECLINED => 'Declined',
        self::PROCESSING => 'Processing',
        self::CONFIRMED => 'Confirmed',
    ];

    private const CLASS_LIST = [
        self::NEW => 'primary',
        self::PENDING => 'warning',
        self::IN_PROGRESS => 'info',
        self::COMPLETED => 'success',
        self::CANCELED => 'awake',
        self::ERROR => 'danger',
        self::DECLINED => 'awake',
        self::PROCESSING => 'info',
        self::CONFIRMED => 'info',
    ];

    public const PROCESSING_LIST = [
        self::PROCESSING,
        self::IN_PROGRESS,
        self::COMPLETED,
        self::CANCELED,
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
        self::DECLINED => 'declined'
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value),
            ['class' => 'badge badge-' . self::getClassName($value)]
        );
    }

    public static function getName(?int $value): string
    {
        return self::LIST[$value] ?? 'Undefined';
    }

    private static function getClassName(?int $value): string
    {
        return self::CLASS_LIST[$value] ?? 'secondary';
    }

    public static function getNames(array $statusIds): array
    {
        $result = [];
        foreach ($statusIds as $value) {
            $result[] = self::getName($value);
        }
        return $result;
    }

    public static function getKeyById(int $id): ?string
    {
        return self::UNIQUE_KEY_LIST[$id] ?? null;
    }

    public static function getClientKeyStatusById(int $id): string
    {
        $key = self::getKeyById($id);
        $statusMap = SettingHelper::getProductQuoteChangeClientStatusMapping();
        return $statusMap[$key] ?? '';
    }
}
