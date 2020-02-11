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

    /**
     * @param int $objectTypeId
     * @return ReasonDto[] array
     */
    public static function getActionList(int $objectTypeId): array
    {
        $list = QaTaskStatusReason::find()->list()->enabled()->processing()->byObjectType($objectTypeId)->column();
    }
}
