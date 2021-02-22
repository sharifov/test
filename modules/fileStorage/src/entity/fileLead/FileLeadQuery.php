<?php

namespace modules\fileStorage\src\entity\fileLead;

use common\models\Employee;
use modules\fileStorage\src\entity\fileClient\FileClient;
use modules\fileStorage\src\entity\fileClient\search\FileClientSearch;
use modules\fileStorage\src\entity\fileUser\FileUser;

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
            ->select([
                'fs_name as name',
                'fs_path as path',
                'fs_title as title',
                'fs_uid as uid',
                'fs_created_dt as created_dt',
                'fs_size as size',
                'fld_fs_id as id',
                'fld_lead_id as lead_id',
                'fus_user_id as user_id',
            ])
            ->innerJoinWith(['file' => static function (\modules\fileStorage\src\entity\fileStorage\Scopes $query) {
                return $query->success();
            }], false)
            ->leftJoin(FileUser::tableName() . ' as user', 'fus_fs_id = fld_fs_id')
            ->byLead($leadId)
            ->orderBy(['id' => SORT_DESC])
            ->indexBy('id')
            ->asArray()
            ->all();
    }
}
