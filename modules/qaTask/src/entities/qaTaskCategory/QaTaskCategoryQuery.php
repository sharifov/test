<?php

namespace modules\qaTask\src\entities\qaTaskCategory;

class QaTaskCategoryQuery
{
    public static function getList(): array
    {
        return QaTaskCategory::find()->list()->column();
    }

    public static function getListEnabled(): array
    {
        return QaTaskCategory::find()->list()->enabled()->column();
    }
}
