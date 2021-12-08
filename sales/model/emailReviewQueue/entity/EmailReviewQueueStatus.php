<?php

namespace sales\model\emailReviewQueue\entity;

use yii\bootstrap4\Html;

class EmailReviewQueueStatus
{
    public const PENDING = 1;
    public const IN_PROGRESS = 2;
    public const REVIEWED = 3;
    public const REJECTED = 4;

    private const LIST = [
        self::PENDING => 'Pending',
        self::IN_PROGRESS => 'In Progress',
        self::REVIEWED => 'Reviewed',
        self::REJECTED => 'Rejected'
    ];

    private const CSS_CLASS_LIST = [
        self::PENDING => 'warning',
        self::IN_PROGRESS => 'info',
        self::REVIEWED => 'success',
        self::REJECTED => 'danger'
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function getName(?int $value): string
    {
        return self::LIST[$value] ?? '--';
    }

    private static function getCssClass(?int $value): string
    {
        return self::CSS_CLASS_LIST[$value] ?? '';
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
