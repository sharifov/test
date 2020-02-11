<?php

namespace modules\qaTask\src\entities\qaTaskActionReason;

class QaTaskActionReasonQuery
{
    public static function getList(): array
    {
        return QaTaskActionReason::find()->list()->column();
    }

    public static function getListWithFullDescription(): array
    {
        return QaTaskActionReason::find()->listWithFullDescription()->all();
    }

    public static function getListEnabled(): array
    {
        return QaTaskActionReason::find()->list()->enabled()->column();
    }

    /**
     * @param int $objectTypeId
     * @return ReasonDto[] array
     */
    public static function getActionList(int $objectTypeId): array
    {
        $list = QaTaskActionReason::find()->list()->enabled()->processing()->byObjectType($objectTypeId)->column();
    }
}
