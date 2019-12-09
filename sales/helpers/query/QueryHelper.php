<?php

namespace sales\helpers\query;

use yii\db\ActiveQuery;

class QueryHelper
{
    public static function dayEqualByUserTZ(ActiveQuery $query, string $dateFieldName, string $value, ?string $userTimeZone): void
    {
        $dateFrom = self::getDateFromUserTZToUtc($value, $userTimeZone);
        $dateTo  = $dateFrom->add(new \DateInterval('P1D'));
        $query->andWhere(['>', $dateFieldName, $dateFrom->format('Y-m-d H:i:s')])->andWhere(['<=', $dateFieldName, $dateTo->format('Y-m-d H:i:s')]);
    }

    public static function dateRangeByUserTZ(
        ActiveQuery $query,
        string $dateFieldName,
        string $valueFrom,
        string $valueTo,
        ?string $userTimeZone
    ): void
    {
        $dateFrom = self::getDateFromUserTZToUtc($valueFrom, $userTimeZone);
        $dateTo = self::getDateFromUserTZToUtc($valueTo, $userTimeZone);
        $query->andWhere(['>=', $dateFieldName, $dateFrom->format('Y-m-d H:i:s')])->andWhere(['<=', $dateFieldName, $dateTo->format('Y-m-d H:i:s')]);
    }

    public static function dateMoreThanByUserTZ(
        ActiveQuery $query,
        string $dateFieldName,
        string $value,
        ?string $userTimeZone
    ): void
    {
        $date = self::getDateFromUserTZToUtc($value, $userTimeZone);
        $query->andWhere(['>=', $dateFieldName, $date->format('Y-m-d H:i:s')]);
    }

    public static function dateLessThanByUserTZ(
        ActiveQuery $query,
        string $dateFieldName,
        string $value,
        ?string $userTimeZone
    ): void
    {
        $date = self::getDateFromUserTZToUtc($value, $userTimeZone);
        $query->andWhere(['<=', $dateFieldName, $date->format('Y-m-d H:i:s')]);
    }

    private static function getDateFromUserTZToUtc(string $value, ?string $userTimeZone): \DateTimeImmutable
    {
        if (!$userTimeZone) {
            $userTimeZone = 'UTC';
        }
        return (new \DateTimeImmutable($value, new \DateTimeZone($userTimeZone)))->setTimezone(new \DateTimeZone('UTC'));
    }
}
