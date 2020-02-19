<?php

namespace modules\qaTask\src\entities\qaTaskActionReason;

class QaTaskActionReasonQuery
{
    public static function getListWithFullDescription(): array
    {
        return QaTaskActionReason::find()->list()->all();
    }

    /**
     * @param int $objectTypeId
     * @param int $actionId
     * @return ReasonDto[]
     */
    public static function getReasons(int $objectTypeId, int $actionId): array
    {
        $reasons = [];
        foreach (QaTaskActionReason::find()->list()->action($actionId)->objectType($objectTypeId)->enabled()->all() as $reason) {
            $reasons[$reason['tar_id']] = new ReasonDto($reason['tar_id'], $reason['tar_name'], $reason['tar_comment_required']);
        }
        return $reasons;
    }
}
