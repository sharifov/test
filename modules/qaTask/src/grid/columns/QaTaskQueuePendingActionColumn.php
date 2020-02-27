<?php

namespace modules\qaTask\src\grid\columns;

use modules\qaTask\src\entities\qaTask\QaTask;
use yii\bootstrap4\Html;

class QaTaskQueuePendingActionColumn extends QaTaskQueueActionColumn
{
    public function __construct($config = [])
    {
        $template = ' {take}';
        $visibleButtons = [
//            'take' => static function ($model, $key, $index) {
//                return true;
//            },
        ];
        $buttons = [
            'take' => static function ($url, QaTask $model) {
                return Html::a('Take', [
                    '/qa-task/qa-task-action/take',
                    'gid' => $model->t_gid
                ], [
                    'class' => 'btn btn-success btn-xs',
                    'target' => '_blank',
                    'data-pjax' => 0,
                    'title' => 'View Task',
                ]);
            },
        ];
        parent::__construct($template, $visibleButtons, $buttons, $config);
    }
}
