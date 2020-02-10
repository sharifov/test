<?php

namespace modules\qaTask\src\helpers\formatters;

use modules\qaTask\src\entities\QaObjectType;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\entities\qaTaskStatusReason\QaTaskStatusReasonQuery;

class QaTaskStatusReasonFormatter
{
    public static function formatListByFullDescription(): array
    {
        $list = [];
        foreach (QaTaskStatusReasonQuery::getListWithFullDescription() as $key => $item) {
            $list[$key] = QaObjectType::getName($item['tsr_object_type_id']) . ' : ' . QaTaskStatus::getName($item['tsr_status_id']) . ' : '  . $item['tsr_name'];
        }
        return $list;
    }
}
