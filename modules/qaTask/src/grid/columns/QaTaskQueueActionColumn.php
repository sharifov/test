<?php

namespace modules\qaTask\src\grid\columns;

use modules\qaTask\src\entities\qaTask\QaTask;
use sales\auth\Auth;
use yii\bootstrap4\Html;
use yii\grid\ActionColumn;

class QaTaskQueueActionColumn extends ActionColumn
{
    public function __construct(
        string $template = '',
        array $visibleButtons = [],
        array $buttons = [],
        $config = []
    )
    {
        $customConfig['template'] = '{viewTask} {viewObject}' . $template;

        $customConfig['visibleButtons'] = array_merge($visibleButtons, [
            'viewTask' => static function ($model, $key, $index) {
                return Auth::can('/qa-task/qa-task/view');
            },
            'viewObject' => static function ($model, $key, $index) {
                return Auth::can('/qa-task/qa-task/view-object');
            },
        ]);

        $customConfig['buttons'] = array_merge($buttons, [
            'viewTask' => static function ($url, QaTask $model) {
                return Html::a('<i class="glyphicon glyphicon-search"></i> Task', [
                    '/qa-task/qa-task/view',
                    'gid' => $model->t_gid
                ], [
                    'class' => 'btn btn-info btn-xs',
                    'target' => '_blank',
                    'data-pjax' => 0,
                    'title' => 'View Task',
                ]);
            },
            'viewObject' => static function ($url, QaTask $model) {
                return Html::a('<i class="glyphicon glyphicon-search"></i> Object',
                    ['/qa-task/qa-task/view-object', 'typeId' => $model->t_object_type_id, 'id' => $model->t_object_id],
                    [
                        'class' => 'btn btn-warning btn-xs',
                        'target' => '_blank',
                        'data-pjax' => 0,
                        'title' => 'View Object',
                    ]);
            },
        ]);
        $config = array_merge($customConfig, $config);
        parent::__construct($config);
    }
}
