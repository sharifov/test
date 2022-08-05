<?php

namespace modules\taskList\src\helpers;

use modules\featureFlag\FFlag;

class TaskListHelper
{
    public static function debug(string $message, string $category)
    {
        /** @fflag FFlag::FF_KEY_DEBUG_ASSIGN_USER_TASK, Debug Assign User Task Enable */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_DEBUG_ASSIGN_USER_TASK)) {
            \Yii::info($message, $category);
        }
    }
}
