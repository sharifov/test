<?php

namespace modules\objectSegment\src\service;

use modules\objectSegment\src\contracts\ObjectSegmentKeyContract;
use modules\objectSegment\src\entities\ObjectSegmentListQuery;
use modules\objectSegment\src\entities\ObjectSegmentRuleQuery;
use modules\objectSegment\src\entities\ObjectSegmentTypeQuery;

class ObjectSegmentListService
{
    /**
     * @param string $typeKey
     * @return array
     */
    public function getTransformedRulesListByTypeKey(string $typeKey): array
    {
        if (!in_array($typeKey, ObjectSegmentKeyContract::TYPE_KEY_LIST)) {
            throw  new \DomainException('type key not found in list');
        }
        $ostIdsQuery = ObjectSegmentTypeQuery::getIdsByKeySqlQuery($typeKey);
        $oslIdsQuery = ObjectSegmentListQuery::getIdsQueryByTypeIdsQuerySql($ostIdsQuery);
        return  ObjectSegmentRuleQuery::getRulesByListIdsQuery($oslIdsQuery);
    }
}
