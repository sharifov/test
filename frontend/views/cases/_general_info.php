<?php

use sales\entities\cases\Cases;
use sales\entities\cases\CasesStatus;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \sales\entities\cases\Cases */
/* @var $isAdmin boolean */
?>

<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-info"></i> General Info</h2>
        <ul class="nav navbar-right panel_toolbox">
            <?php if($model->isProcessing()):?>
            <li>
                <?= \yii\bootstrap\Html::a('<i class="fa fa-edit warning"></i> Update', '#', ['id' => 'btn-case-update', 'title' => 'Update Case'])?>
            </li>
            <?php endif; ?>
            <li>
                <a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
            </li>
            <?/*<li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-wrench"></i></a>
                <ul class="dropdown-menu" role="menu">
                    <li><a href="#">Settings 1</a>
                    </li>
                    <li><a href="#">Settings 2</a>
                    </li>
                </ul>
            </li>
            <li><a class="close-link"><i class="fa fa-close"></i></a>
            </li>*/?>
        </ul>
        <div class="clearfix"></div>
    </div>
    <div class="x_content" style="display: block;">
        <?php if($model):?>
        <div class="row">
            <div class="col-md-12">
                <h4>Category: <span style="color: #0a0a0a"><?=$model->category ? Html::encode($model->category->cc_name) : '' ?></span></h4>
                <h4>Subject: <span style="color: #0a0a0a"><?=$model->cs_subject ? Html::encode($model->cs_subject) : '' ?></span></h4>
                <pre><?=$model->cs_description ? nl2br(trim($model->cs_description)) : '' ?></pre>
            </div>
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        //'cs_id',
                        [
                            'attribute' => 'cs_status',
                            'value' => function (Cases $model) {
                                return CasesStatus::getLabel($model->cs_status);
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'cs_project_id',
                            'value' => function (Cases $model) {
                                return $model->project ? '<span class="badge badge-info">' . $model->project->name .'</span>' : '';
                            },
                            'format' => 'raw'
                        ],
                        [
                                'label' => 'Agent',
                            'attribute' => 'cs_user_id',
                            'value' => function (Cases $model) {
                                return $model->owner ? '<i class="fa fa-user"></i> ' . Html::encode($model->owner->username) : '-';
                            },
                            'format' => 'raw'
                        ],
                        //'cs_subject',
                        //'cs_description:ntext',
                    ],
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        //'cs_category',
                        //'cs_lead_id',
                        //'cs_call_id',
                        //'cs_dep_id',
                        [
                            'attribute' => 'cs_dep_id',
                            'value' => function (Cases $model) {
                                return $model->department ? $model->department->dep_name : '';
                            },
                        ],
                        //'cs_client_id',
                        //'cs_created_dt',
                        [
                            'attribute' => 'cs_created_dt',
                            'value' => function (Cases $model) {
                                return $model->cs_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cs_created_dt)) : '-';
                            },
                            'format' => 'raw'
                        ],
                        [
                            'attribute' => 'cs_updated_dt',
                            'value' => function (Cases $model) {
                                return $model->cs_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->cs_updated_dt)) : '-';
                            },
                            'format' => 'raw'
                        ],

                        //'cs_updated_dt',
                    ],
                ]) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>


<?php
$caseUpdateAjaxUrl = \yii\helpers\Url::to(['cases/ajax-update', 'gid' => $model->cs_gid]);

$js = <<<JS

    $(document).on('click', '#btn-case-update', function(){
            var modal = $('#modalCaseSm');
            //$('#search-sale-panel').toggle();
            modal.modal('show').find('.modal-body').html('<div style="text-align:center;font-size: 60px;"><i class="fa fa-spin fa-spinner"></i> Loading ...</div>');
            modal.modal('show').find('.modal-header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
            
            $.get('$caseUpdateAjaxUrl', function(data) {
                modal.find('.modal-body').html(data);
            });
            
           return false;
     });

JS;

$this->registerJs($js);
