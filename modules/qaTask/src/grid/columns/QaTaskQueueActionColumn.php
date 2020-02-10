<?php

namespace modules\qaTask\src\grid\columns;

use modules\qaTask\src\entities\qaTask\QaTask;
use yii\bootstrap4\Html;
use yii\grid\ActionColumn;

class QaTaskQueueActionColumn extends ActionColumn
{
    public function __construct($config = [])
    {
        $config['template'] = '{view}';
        $config['visibleButtons'] = [
            /*'view' => function ($model, $key, $index) {
                return true;
            },*/
        ];
        $config['buttons'] = [
            'view' => static function ($url, QaTask $model) {
                return Html::a('<i class="glyphicon glyphicon-search"></i> View Task', [
                    '/qa-task/qa-task/view',
                    'gid' => $model->t_gid
                ], [
                    'class' => 'btn btn-info btn-xs',
                    'target' => '_blank',
                    'data-pjax' => 0,
                    'title' => 'View',
                ]);
            },
        ];
        parent::__construct($config);
    }
}
