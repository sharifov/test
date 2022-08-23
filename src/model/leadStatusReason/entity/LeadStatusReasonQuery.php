<?php

namespace src\model\leadStatusReason\entity;

use yii\helpers\ArrayHelper;

class LeadStatusReasonQuery
{
    public static function getLeadStatusReasonByKey(string $key): ?LeadStatusReason
    {
        return LeadStatusReason::find()->byKey($key)->enabled()->one();
    }

    public static function getAllEnabledAsArray(): array
    {
        return LeadStatusReason::find()->enabled()->asArray()->all();
    }

    public static function getList(string $key = 'lsr_key', string $value = 'lsr_name'): array
    {
        return ArrayHelper::map(self::getAllEnabledAsArray(), $key, $value);
    }
}
