<?php

namespace modules\qaTask\src\entities\qaTask;

use common\models\Lead;
use sales\entities\cases\Cases;
use yii\bootstrap4\Html;

class QaTaskObjectType
{
    public const LEAD = 1;
    public const CASE = 2;

    private const LIST = [
        self::LEAD => 'Lead',
        self::CASE => 'Case',
    ];

    private const CLASS_LIST = [
        self::LEAD => 'info',
        self::CASE => 'warning',
    ];

    private const MAP = [
        self::LEAD => Lead::class,
        self::CASE => Cases::class,
    ];

    public static function getList(): array
    {
        return self::LIST;
    }

    public static function isExist(int $typeId): bool
    {
        return isset(self::getList()[$typeId]);
    }

    public static function asFormat(?int $value): string
    {
        return Html::tag(
            'span',
            self::getName($value),
            ['class' => 'badge badge-' . self::getCssClass($value)]
        );
    }

    public static function getName(?int $value)
    {
        return self::LIST[$value] ?? 'Undefined';
    }

    private static function getCssClass(?int $value): string
    {
        return self::CLASS_LIST[$value] ?? 'secondary';
    }

    public static function isLead(int $type): bool
    {
        return $type === self::LEAD;
    }

    public static function isCase(int $type): bool
    {
        return $type === self::CASE;
    }

    public static function getObjectClass(int $type): string
    {
        if (!isset(self::MAP[$type])) {
            throw new \DomainException('Undefined Object Type');
        }

        return self::MAP[$type];
    }
}
