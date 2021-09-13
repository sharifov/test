<?php

namespace modules\product\src\entities\productQuoteChange;

use yii\bootstrap4\Html;

class ProductQuoteChangeStatus
{
    public const NEW = 1;
    public const DECISION_PENDING = 2;
    public const IN_PROGRESS = 3;
    public const COMPLETE = 4;
    public const CANCELED = 5;
    public const ERROR = 6;
    public const DECLINED = 7;
    public const DECIDED = 8;

    public const LIST = [
        self::NEW => 'New',
        self::DECISION_PENDING => 'Decision pending',
        self::IN_PROGRESS => 'In progress',
        self::COMPLETE => 'Complete',
        self::CANCELED => 'Canceled',
        self::ERROR => 'Error',
        self::DECLINED => 'Declined',
        self::DECIDED => 'Decided',
    ];

    private const CLASS_LIST = [
        self::NEW => 'info',
        self::DECISION_PENDING => 'warning',
        self::IN_PROGRESS => 'info',
        self::COMPLETE => 'success',
        self::CANCELED => 'danger',
        self::ERROR => 'danger',
        self::DECLINED => 'danger',
        self::DECIDED => 'info',
    ];

    public const PROCESSING_LIST = [
        self::DECIDED,
        self::IN_PROGRESS,
        self::COMPLETE,
        //self::DECISION_PENDING
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
}
