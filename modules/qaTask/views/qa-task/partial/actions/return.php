<?php

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\useCases\qaTask\returnTask\toEscalate\QaTaskReturnToEscalateService;
use modules\qaTask\src\useCases\qaTask\returnTask\toPending\QaTaskReturnToPendingService;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

?>

<?php if (QaTaskReturnToPendingService::can(Auth::user(), $model)): ?>
    <div class="btn-group" style="margin-bottom: 5px; margin-left: 7px;">
        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            Return
        </button>
        <div class="dropdown-menu">
            <?= Html::a(
                'To Pending',
                '#',
                [
                    'class' => 'dropdown-item btn-modal-show btn-' . QaTaskStatus::getCssClass(QaTaskStatus::PENDING),
                    'title' => 'Return to Pending',
                    'data-url' => Url::to(['/qa-task/qa-task-action/return-to-pending', 'gid' => $model->t_gid]),
                    'data-title' => 'Task [' . $model->t_id . '] return to Pending',
                    'data-modal-id' => 'modal-df',
                ]
            ) ?>
            <?php if (QaTaskReturnToEscalateService::can(Auth::user(), $model)): ?>
                <?= Html::a(
                    'To Escalate',
                    '#',
                    [
                        'class' => 'dropdown-item btn-modal-show btn-' . QaTaskStatus::getCssClass(QaTaskStatus::ESCALATED),
                        'title' => 'Return to Escalate',
                        'data-url' => Url::to(['/qa-task/qa-task-action/return-to-escalate', 'gid' => $model->t_gid]),
                        'data-title' => 'Task [' . $model->t_id . '] return to Escalate',
                        'data-modal-id' => 'modal-df',
                    ]
                ) ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php
