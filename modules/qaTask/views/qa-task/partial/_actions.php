<?php

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\useCases\qaTask\cancel\QaTaskCancelService;
use modules\qaTask\src\useCases\qaTask\close\QaTaskCloseService;
use modules\qaTask\src\useCases\qaTask\escalate\QaTaskEscalateService;
use modules\qaTask\src\useCases\qaTask\returnTask\toEscalate\QaTaskReturnToEscalateService;
use modules\qaTask\src\useCases\qaTask\returnTask\toPending\QaTaskReturnToPendingService;
use modules\qaTask\src\useCases\qaTask\take\QaTaskTakeService;
use modules\qaTask\src\useCases\qaTask\takeOver\QaTaskTakeOverService;
use sales\auth\Auth;
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

<?php if (QaTaskTakeService::can($model, Auth::id())): ?>
    <?= Html::a(
        'Take',
        Url::to(['/qa-task/qa-task-action/take', 'gid' => $model->t_gid]),
        [
            'class' => 'btn btn-' . QaTaskStatus::getCssClass(QaTaskStatus::PROCESSING),
            'title' => 'Take',
        ]
    ) ?>
<?php endif; ?>

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

<?php if (QaTaskCancelService::can($model, Auth::id())): ?>
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

<?php if (QaTaskReturnToPendingService::can($model, Auth::id())): ?>
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
            <?php if (QaTaskReturnToEscalateService::can($model, Auth::id())): ?>
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
$js = <<<JS

 $(document).on('click', '.btn-modal-show', function(e){        
    e.preventDefault();
    
    let url = $(this).data('url');
    let title = $(this).data('title');
    let modalId = $(this).data('modal-id');
    let modal = $('#' + modalId);
      
    modal.find('.modal-body').html('');
    modal.find('.modal-title').html(title);
    modal.find('.modal-body').load(url, function( response, status, xhr ) {
        //$('#preloader').addClass('d-none');
        if (status == 'error') {
            alert(response);
        } else {
            modal.modal({
              backdrop: 'static',
              show: true
            });
        }
    });
 });

JS;
$this->registerJs($js);
