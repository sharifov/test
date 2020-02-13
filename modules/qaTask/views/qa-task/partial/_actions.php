<?php

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\useCases\qaTask\take\QaTaskTakeService;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

?>

<?= Html::button(
    '<i class="fa fa-list"></i> Status History ' . ($model->statusLogs ? '(' . count($model->statusLogs) . ')' : ''),
    [
        'class' => 'btn btn-secondary',
        'id' => 'btn-status-history',
        'title' => 'Status history',
        'data-url' => Url::to(['/qa-task/qa-task-status-log/show', 'gid' => $model->t_gid]),
        'data-id' => $model->t_id,
        'data-gid' => $model->t_gid,
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

<?php
$js = <<<JS
 $(document).on('click', '#btn-status-history', function(e){        
        e.preventDefault();
        let url = $(this).data('url');
        let id = $(this).data('id');
        let gid = $(this).data('gid');
        let modal = $('#modal-lg');
          
        modal.find('.modal-body').html('');
        modal.find('.modal-title').html('Task [' + id + '] status history');
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
