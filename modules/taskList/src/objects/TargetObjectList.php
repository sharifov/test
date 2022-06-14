<?php

namespace modules\taskList\src\objects;

use common\models\Department;
use common\models\EmailTemplateType;
use common\models\Project;

class TargetObjectList
{
    public const TARGET_OBJ_LEAD = 1;
    public const TARGET_OBJ_CASE = 2;

    public const ALL_TARGET_OBJ_LIST = [
        self::TARGET_OBJ_LEAD => 'Lead',
        self::TARGET_OBJ_CASE => 'Case',
    ];

    /**
     * @param int $objectId
     * @return string
     */
    public static function getTargetName(int $objectId): string
    {
        return self::ALL_TARGET_OBJ_LIST[$objectId] ?? '';
    }

    /**
     * @return string[]
     */
    public static function getAllTargetObjectList(): array
    {
        return self::ALL_TARGET_OBJ_LIST;
    }

    /**
     * @param array $targetObjectListId
     * @return array
     */
    public static function getTargetObjectListByIds(array $targetObjectListId = []): array
    {
        return array_filter(self::getAllTargetObjectList(), function ($k) use ($targetObjectListId) {
            return in_array($k, $targetObjectListId);
        }, ARRAY_FILTER_USE_KEY);
    }
}
