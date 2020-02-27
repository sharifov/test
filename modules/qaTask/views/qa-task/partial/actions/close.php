<?php

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\useCases\qaTask\close\QaTaskCloseService;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

?>

<?php if (QaTaskCloseService::can($model, Auth::id())): ?>
    <?= Html::button(
        'Close',
        [
            'class' => 'btn-modal-show btn btn-' . QaTaskStatus::getCssClass(QaTaskStatus::CLOSED),
            'title' => 'Close',
            'data-url' => Url::to(['/qa-task/qa-task-action/close', 'gid' => $model->t_gid]),
            'data-title' => 'Task [' . $model->t_id . '] close',
            'data-modal-id' => 'modal-df',
        ]
    ) ?>
<?php endif; ?>

<?php
