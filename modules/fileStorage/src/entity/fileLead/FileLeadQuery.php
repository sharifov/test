<?php

namespace modules\fileStorage\src\entity\fileLead;

class FileLeadQuery
{
    public static function getLead(int $fileId): ?int
    {
        $lead = FileLead::find()->select(['fld_lead_id'])->byFile($fileId)->asArray()->one();
        return $lead ? (int)$lead['fld_lead_id'] : null;
    }
}
