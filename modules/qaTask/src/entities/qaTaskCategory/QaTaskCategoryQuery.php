<?php

namespace modules\qaTask\src\entities\qaTaskCategory;

use modules\qaTask\src\entities\qaTask\QaTaskObjectType;

class QaTaskCategoryQuery
{
    /**
     * @return array ['id' => 'name']
     */
    public static function getSimpleList(): array
    {
        return QaTaskCategory::find()->list()->column();
    }

    public static function getSimpleListEnabled(): array
    {
        return QaTaskCategory::find()->list()->enabled()->column();
    }

    public static function getEnabledListByLead(): array
    {
        return self::getEnabledListByType(QaTaskObjectType::LEAD);
    }

    public static function getEnabledListByType(int $value): array
    {
        return QaTaskCategory::find()->list()->byType($value)->enabled()->column();
    }

    /**
     * @return array
        [
            1 => [
                'tc_name' => 'Name'
                'tc_object_type_id' => 2
                'tc_id' => '1'
            ]
            ...
        ]
     */
    public static function getList(): array
    {
        return QaTaskCategory::find()->list()->all();
    }

    public static function getListEnabled(): array
    {
        return QaTaskCategory::find()->list()->enabled()->all();
    }

    public static function getCategoryIdByKey(string $key): ?int
    {
        if ($category = QaTaskCategory::find()->select(['tc_id'])->byKey($key)->limit(1)->one()) {
            return $category['tc_id'];
        }
        return null;
    }
}
