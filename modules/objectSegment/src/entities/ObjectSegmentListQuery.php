<?php

namespace modules\objectSegment\src\entities;

class ObjectSegmentListQuery
{
    public static function getIdsQueryByTypeIdsQuerySql(string $typeIdsQuerySql): string
    {
        return ObjectSegmentList
            ::find()
            ->select('osl_id')
            ->where('osl_ost_id IN (' . $typeIdsQuerySql . ')')
            ->andWhere(['osl_enabled' => true])
            ->createCommand()
            ->rawSql;
    }

    public static function getObjectList(): array
    {
        $items  = ObjectSegmentList::find()->all();
        $result = [];
        foreach ($items as $item) {
            $result[$item->osl_id] = $item->osl_title;
        }
        return $result;
    }
}
