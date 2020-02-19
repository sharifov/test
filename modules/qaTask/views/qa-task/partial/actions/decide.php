<?php

use modules\qaTask\src\useCases\qaTask\decide\lead\reAssign\QaTaskDecideLeadReAssignService;
use modules\qaTask\src\useCases\qaTask\decide\lead\sendToRedialQueue\QaTaskDecideLeadSendToRedialQueue;
use modules\qaTask\src\useCases\qaTask\decide\noAction\QaTaskDecideNoActionService;
use modules\qaTask\src\useCases\qaTask\decide\QaTaskDecideService;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

?>

<?php if (QaTaskDecideService::can($model, Auth::id())): ?>
    <div class="btn-group" style="margin-bottom: 5px; margin-left: 7px;">
        <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            Decide
        </button>
        <div class="dropdown-menu">
            <?php if (QaTaskDecideNoActionService::can($model, Auth::id())): ?>
                <?= Html::a(
                    'No Action',
                    '#',
                    [
                        'class' => 'dropdown-item btn-modal-show',
                        'title' => 'No Action',
                        'data-url' => Url::to(['/qa-task/qa-task-action/decide-no-action', 'gid' => $model->t_gid]),
                        'data-title' => 'Task [' . $model->t_id . '] Decide. No action',
                        'data-modal-id' => 'modal-df',
                    ]
                ) ?>
            <?php endif; ?>
            <?php if (QaTaskDecideLeadSendToRedialQueue::can($model, Auth::id())): ?>
                <?= Html::a(
                    'Send Lead to Redial Queue',
                    ['/qa-task/qa-task-action/decide-lead-send-to-redial-queue', 'gid' => $model->t_gid],
                    [
                        'class' => 'dropdown-item',
                        'title' => 'Decide. Send Lead to Redial Queue',
                    ]
                ) ?>
            <?php endif; ?>
            <?php if (QaTaskDecideLeadReAssignService::can($model, Auth::id())): ?>
                <?= Html::a(
                    'Re-assign Lead',
                    '#',
                    [
                        'class' => 'dropdown-item btn-modal-show',
                        'title' => 'Re-assign Lead',
                        'data-url' => Url::to(['/qa-task/qa-task-action/decide-lead-re-assign', 'gid' => $model->t_gid]),
                        'data-title' => 'Task [' . $model->t_id . '] Decide. Re-assign Lead',
                        'data-modal-id' => 'modal-df',
                    ]
                ) ?>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php
