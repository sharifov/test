<?php

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\useCases\qaTask\cancel\QaTaskCancelService;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

?>

<?php if (QaTaskCancelService::can($model, Auth::user())): ?>
    <?= Html::button(
        'Cancel',
        [
            'class' => 'btn-modal-show btn btn-' . QaTaskStatus::getCssClass(QaTaskStatus::CANCELED),
            'title' => 'Cancel',
            'data-url' => Url::to(['/qa-task/qa-task-action/cancel', 'gid' => $model->t_gid]),
            'data-title' => 'Task [' . $model->t_id . '] cancel',
            'data-modal-id' => 'modal-df',
        ]
    ) ?>
<?php endif; ?>

<?php
