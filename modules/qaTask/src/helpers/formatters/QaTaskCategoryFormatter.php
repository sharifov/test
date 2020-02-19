<?php

namespace modules\qaTask\src\helpers\formatters;

use modules\qaTask\src\entities\qaTask\QaTaskObjectType;

class QaTaskCategoryFormatter
{
    public static function format(array $list): array
    {
        $out = [];
        foreach ($list as $key => $item) {
            if (isset($item['tc_object_type_id'], $item['tc_name'])) {
                $out[$key] = QaTaskObjectType::getName($item['tc_object_type_id']) . ': ' . $item['tc_name'];
            } else {
                $out[$key] = $item;
            }
        }
        return $out;
    }
}
