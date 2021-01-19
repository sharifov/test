<?php

namespace modules\fileStorage\src\entity\fileCase;

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
            ->select(['fs_name as name', 'fs_path as path', 'fs_title as title', 'fc_fs_id as id'])
            ->innerJoinWith('file', false)
            ->byCase($caseId)
            ->orderBy(['id' => SORT_DESC])
            ->indexBy('id')
            ->asArray()
            ->all();
    }
}
