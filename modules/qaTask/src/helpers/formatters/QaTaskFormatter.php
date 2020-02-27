<?php

namespace modules\qaTask\src\helpers\formatters;

use modules\qaTask\src\entities\qaTask\QaTask;
use yii\bootstrap4\Html;

class QaTaskFormatter
{
    public static function asQaTask(QaTask $task): string
    {
        return Html::a(
            'task: ' . $task->t_id,
            ['/qa-task/qa-task-crud/view', 'id' => $task->t_id],
            ['target' => '_blank', 'data-pjax' => 0]
        );
    }
}
