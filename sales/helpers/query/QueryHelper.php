<?php

namespace sales\helpers\query;

use Yii;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;

class QueryHelper
{
    /**
     *
     * Ex. rules:
      ['created_dt', 'date', 'format' => 'php:Y-m-d'],
     *
     * Ex.
      if ($this->from) {
        \sales\helpers\query\QueryHelper::dayEqualByUserTZ($query, 'from', $this->from, $user->timezone);
      }
     *
     */
    public static function dayEqualByUserTZ(ActiveQuery $query, string $dateFieldName, string $value, ?string $userTimeZone): void
    {
        $dateFrom = self::getDateFromUserTZToUtc($value, $userTimeZone);
        $dateTo = $dateFrom->add(new \DateInterval('P1D'));
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

    public static function getQueryCountValidModel(Model $model, string $prefix, Query $query, int $duration = 600)
    {
        $hash = self::getFieldsHash($model, $prefix);
        return Yii::$app->cache->getOrSet($hash, static function () use ($query) {
            $query = clone $query;
            return (int) $query->limit(-1)->offset(-1)->orderBy([])->count('*');

        }, $duration);
    }

    public static function getQueryCountInvalidModel(Model $model, string $prefix, Query $query, int $duration = 600)
    {
        $clone = clone $model;
        self::resetProperties($clone);
        return self::getQueryCountValidModel($clone, $prefix, $query, $duration);
    }

    private static function resetProperties(Model $model): void
    {
        foreach (self::getFieldsFromRules($model->rules()) as $rule) {
            try {
                $model->{$rule} = null;
            } catch (\Throwable $e) {
                \Yii::error($e, 'QueryHelper:getFieldsHash');
            }
        }
    }

    private static function getFieldsHash(Model $model, string $prefix): string
    {
        $fields = [];
        foreach (self::getFieldsFromRules($model->rules()) as $rule) {
            try {
                $fields[$rule] = $model->{$rule};
            } catch (\Throwable $e) {
                \Yii::error($e, 'QueryHelper:getFieldsHash');
            }
        }
        return base64_encode($prefix . '_' . serialize($fields));
    }

    private static function getFieldsFromRules(array $rules): array
    {
        $fields = [];
        foreach ($rules as $rule) {
            $item = $rule[0];
            if (is_array($item)) {
                foreach ($item as $value) {
                    $fields[] = $value;
                }
            } else {
                $fields[] = $item;
            }
        }
        return $fields;
    }
}
