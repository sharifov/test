<?php

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\useCases\qaTask\takeOver\QaTaskTakeOverService;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

?>

<?php if (QaTaskTakeOverService::can($model, Auth::id())): ?>
    <?= Html::button(
        'Take over',
        [
            'class' => 'btn-modal-show btn btn-' . QaTaskStatus::getCssClass(QaTaskStatus::PROCESSING),
            'title' => 'Take over',
            'data-url' => Url::to(['/qa-task/qa-task-action/take-over', 'gid' => $model->t_gid]),
            'data-title' => 'Task [' . $model->t_id . '] take over',
            'data-modal-id' => 'modal-df',
        ]
    ) ?>
<?php endif; ?>

<?php
