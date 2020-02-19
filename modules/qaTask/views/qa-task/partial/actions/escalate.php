<?php

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\useCases\qaTask\escalate\QaTaskEscalateService;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

?>

<?php if (QaTaskEscalateService::can($model, Auth::id())): ?>
    <?= Html::button(
        'Escalate',
        [
            'class' => 'btn-modal-show btn btn-' . QaTaskStatus::getCssClass(QaTaskStatus::ESCALATED),
            'title' => 'Escalate',
            'data-url' => Url::to(['/qa-task/qa-task-action/escalate', 'gid' => $model->t_gid]),
            'data-title' => 'Task [' . $model->t_id . '] escalate',
            'data-modal-id' => 'modal-df',
        ]
    ) ?>
<?php endif; ?>

<?php
