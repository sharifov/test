<?php

namespace modules\qaTask\src\helpers\formatters;

use modules\qaTask\src\entities\qaTask\QaTaskObjectType;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReasonQuery;

class QaTaskStatusReasonFormatter
{
    public static function formatListByFullDescription(): array
    {
        $list = [];
        foreach (QaTaskActionReasonQuery::getListWithFullDescription() as $key => $item) {
            $list[$key] = QaTaskObjectType::getName($item['tar_object_type_id']) . ' : ' . QaTaskStatus::getName($item['tar_action_id']) . ' : '  . $item['tar_name'];
        }
        return $list;
    }
}
