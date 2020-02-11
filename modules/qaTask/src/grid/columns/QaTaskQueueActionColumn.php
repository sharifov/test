<?php

namespace modules\qaTask\src\grid\columns;

use modules\qaTask\src\entities\qaTask\QaTask;
use yii\bootstrap4\Html;
use yii\grid\ActionColumn;

class QaTaskQueueActionColumn extends ActionColumn
{
    public function __construct($config = [])
    {
        $config['template'] = '{viewTask} {viewObject}';
        $config['visibleButtons'] = [
            /*'view' => function ($model, $key, $index) {
                return true;
            },*/
        ];
        $config['buttons'] = [
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
        ];
        parent::__construct($config);
    }
}
