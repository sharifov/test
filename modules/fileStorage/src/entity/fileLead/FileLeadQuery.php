<?php

namespace modules\fileStorage\src\entity\fileLead;

class FileLeadQuery
{
    public static function getLead(int $fileId): ?int
    {
        $lead = FileLead::find()->select(['fld_lead_id'])->byFile($fileId)->asArray()->one();
        return $lead ? (int)$lead['fld_lead_id'] : null;
    }

    public static function getListByLead(int $leadId): array
    {
        return FileLead::find()
            ->select(['fs_name as name', 'fs_path as path', 'fs_title as title', 'fld_fs_id as id'])
            ->innerJoinWith('file', false)
            ->byLead($leadId)
            ->orderBy(['id' => SORT_DESC])
            ->indexBy('id')
            ->asArray()
            ->all();
    }
}
