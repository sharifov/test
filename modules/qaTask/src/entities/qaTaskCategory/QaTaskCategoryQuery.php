<?php

namespace modules\qaTask\src\entities\qaTaskCategory;

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
}
