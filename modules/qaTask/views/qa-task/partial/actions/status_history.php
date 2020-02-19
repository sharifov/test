<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

?>

<?= Html::button(
    '<i class="fa fa-list"></i> Status History ' . ($model->statusLogs ? '(' . count($model->statusLogs) . ')' : ''),
    [
        'class' => 'btn-modal-show btn btn-secondary',
        'title' => 'Status history',
        'data-url' => Url::to(['/qa-task/qa-task-status-log/show', 'gid' => $model->t_gid]),
        'data-title' => 'Task [' . $model->t_id . '] status history',
        'data-modal-id' => 'modal-lg',
    ]
) ?>

<?php
