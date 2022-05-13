<?php

namespace modules\objectSegment\src\entities;

class ObjectSegmentTypeQuery
{
    public static function getIdsByKeySqlQuery(string $key): string
    {
        return ObjectSegmentType
            ::find()
            ->select('ost_id')
            ->where(['ost_key' => $key])
            ->createCommand()
            ->rawSql;
    }

    public static function getObjectNameByOsrOslId(int $osrOslId): ?string
    {
        $osl = ObjectSegmentList::findOne($osrOslId);
        if (empty($osl)) {
            return null;
        }
        $ost = ObjectSegmentType::findOne($osl->osl_ost_id);
        if (empty($ost)) {
            return null;
        }
        return $ost->ost_key;
    }

    public static function getObjectTypesList(): array
    {
        $types  = ObjectSegmentType::find()->all();
        $result = [];
        foreach ($types as $type) {
            $result[$type->ost_id] = $type->ost_key;
        }
        return $result;
    }
}
