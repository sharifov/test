<?php

/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

use yii\helpers\Html;
use yii\helpers\Url;

?>

<?= Html::a(
    '<i class="fa fa-user"></i> User Assign',
    '#',
    [
        'class' => 'btn btn-modal-show btn-warning',
        'title' => 'User Assign',
        'data-url' => Url::to(['/qa-task/qa-task-action/user-assign', 'gid[]' => $model->t_gid]),
        'data-title' => 'Task [' . $model->t_id . '] User Assign',
        'data-modal-id' => 'modal-df',
    ]
);
