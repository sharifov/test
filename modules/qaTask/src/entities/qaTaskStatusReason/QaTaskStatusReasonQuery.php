<?php

namespace modules\qaTask\src\entities\qaTaskStatusReason;

class QaTaskStatusReasonQuery
{
    public static function getList(): array
    {
        return QaTaskStatusReason::find()->list()->column();
    }

    public static function getListWithFullDescription(): array
    {
        return QaTaskStatusReason::find()->listWithFullDescription()->all();
    }

    public static function getListEnabled(): array
    {
        return QaTaskStatusReason::find()->list()->enabled()->column();
    }
}
