<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTask\QaTask */

$this->title = 'Task ' . $model->t_id . ' [' . $model->t_gid . ']';
$this->params['breadcrumbs'][] = ['label' => 'Qa Tasks', 'url' => ['/qa-task/qa-task-queue/search']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
    <div class="qa-task-view">

        <h1>
            <?= Html::encode($this->title) ?>
        </h1>

        <div class="x_panel">
            <div class="x_content" style="display: block;">

                <?= Html::button(
                    '<i class="fa fa-list"></i> Status History ' . ($model->statusLogs ? '(' . count($model->statusLogs) . ')' : ''),
                    [
                        'class' => 'btn btn-info',
                        'id' => 'btn-status-history',
                        'title' => 'Status history',
                        'data-url' => Url::to(['/qa-task/qa-task-status-log/show', 'gid' => $model->t_gid]),
                        'data-id' => $model->t_id,
                        'data-gid' => $model->t_gid,
                    ]
                ) ?>

            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <?= $this->render('partial/_general_info', [
                    'model' => $model,
                ])
                ?>
            </div>
        </div>
    </div>

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
