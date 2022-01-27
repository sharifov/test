<?php

namespace src\model\leadPoorProcessingData\entity;

/**
 * Class LeadPoorProcessingDataQuery
 */
class LeadPoorProcessingDataQuery
{
    public static function getList(int $cacheDuration = -1): array
    {
        return LeadPoorProcessingData::find()
            ->select(['lppd_key', 'lppd_id'])
            ->orderBy(['lppd_key' => SORT_ASC])
            ->indexBy('lppd_id')
            ->cache($cacheDuration)
            ->asArray()
            ->column();
    }

    public static function getRuleByKey(string $key): ?LeadPoorProcessingData
    {
        return LeadPoorProcessingData::find()
            ->where(['lppd_key' => $key])
            ->limit(1)
            ->one()
        ;
    }
}
