<?php

namespace modules\objectSegment\src\entities;

class ObjectSegmentRuleQuery
{
    public static function getRulesByListIdsQuery(string $listIdsQuery): array
    {
        return ObjectSegmentRule
            ::find()
            ->where('osr_osl_id IN (' . $listIdsQuery . ')')
            ->andWhere(['osr_enabled' => true])
            ->joinWith('osrObjectSegmentList')
            ->asArray()
            ->all();
    }
}
