<?php

namespace modules\fileStorage\src\entity\fileCase;

use modules\fileStorage\src\entity\fileUser\FileUser;

class FileCaseQuery
{
    public static function getCase(int $fileId): ?int
    {
        $case = FileCase::find()->select(['fc_case_id'])->byFile($fileId)->asArray()->one();
        return $case ? (int)$case['fc_case_id'] : null;
    }

    public static function getListByCase(int $caseId): array
    {
        return FileCase::find()
            ->select([
                'fs_name as name',
                'fs_path as path',
                'fs_title as title',
                'fs_uid as uid',
                'fs_created_dt as created_dt',
                'fs_size as size',
                'fc_fs_id as id',
                'fc_case_id as case_id',
                'fus_user_id as user_id',
            ])
            ->innerJoinWith(['file' => static function (\modules\fileStorage\src\entity\fileStorage\Scopes $query) {
                return $query->success();
            }], false)
            ->leftJoin(FileUser::tableName() . ' as user', 'fus_fs_id = fc_fs_id')
            ->byCase($caseId)
            ->orderBy(['id' => SORT_DESC])
            ->indexBy('id')
            ->asArray()
            ->all();
    }
}
