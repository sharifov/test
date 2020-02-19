<?php

namespace modules\qaTask\src\entities\qaTask;

use yii\bootstrap4\Html;

class QaTaskRating
{
    public const BAD = 1;
    public const AVERAGE = 2;
    public const GOOD = 3;

    private const LIST = [
        self::BAD => 'Bad',
        self::AVERAGE => 'Average',
        self::GOOD => 'Good',
    ];

    private const CSS_CLASS_LIST = [
        self::BAD => 'danger',
        self::AVERAGE => 'warning',
        self::GOOD => 'success',
    ];

    public static function guard(int $rating): void
    {
        if (!isset(self::LIST[$rating])) {
            throw new \DomainException('Undefined rating.');
        }
    }

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function getName(?int $value)
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
