<?php

namespace modules\fileStorage\src\entity\fileCase;

class FileCaseQuery
{
    public static function getCase(int $fileId): ?int
    {
        $case = FileCase::find()->select(['fc_case_id'])->byFile($fileId)->asArray()->one();
        return $case ? (int)$case['fc_case_id'] : null;
    }
}
