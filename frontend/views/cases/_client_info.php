<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $caseModel \sales\entities\cases\Cases */
/* @var $isAdmin boolean */
?>

<div class="x_panel">
    <div class="x_title">
        <h2><i class="fa fa-user"></i> Client Info</h2>
        <ul class="nav navbar-right panel_toolbox">
            <?php if($caseModel->isProcessing()):?>
            <li>
                <?= \yii\bootstrap\Html::a('<i class="fa fa-plus-circle success"></i> Add Email', '#', ['id' => 'btn-add-email', 'title' => 'Add Email'])?>
            </li>
            <li>
                <?= \yii\bootstrap\Html::a('<i class="fa fa-edit warning"></i> Update', '#', ['id' => 'btn-client-update', 'title' => 'Update Client Info'])?>
            </li>
            <?php endif;?>
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
        <?php if($caseModel->client):?>
        <div class="row">
            <div class="col-md-6">
                <?= \yii\widgets\DetailView::widget([
                    'model' => $caseModel->client,
                    'attributes' => [
                        'id',
                        'first_name',
                        'middle_name',
                        'last_name',
                    ],
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= \yii\widgets\DetailView::widget([
                    'model' => $caseModel->client,
                    'attributes' => [
                        [
                            'label' => 'Phones',
                            'value' => function(\common\models\Client $model) {

                                $phones = $model->clientPhones;
                                $data = [];
                                if($phones) {
                                    foreach ($phones as $k => $phone) {
                                        $data[] = '<i class="fa fa-phone"></i> <code>'.Html::encode($phone->phone).'</code>'; //<code>'.Html::a($phone->phone, ['client-phone/view', 'id' => $phone->id], ['target' => '_blank', 'data-pjax' => 0]).'</code>';
                                    }
                                }

                                $str = implode('<br>', $data);
                                return ''.$str.'';
                            },
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-left'],
                        ],

                        [
                            'label' => 'Emails',
                            'value' => function(\common\models\Client $model) {

                                $emails = $model->clientEmails;
                                $data = [];
                                if($emails) {
                                    foreach ($emails as $k => $email) {
                                        $data[] = '<i class="fa fa-envelope"></i> <code>'.Html::encode($email->email).'</code>';
                                    }
                                }

                                $str = implode('<br>', $data);
                                return ''.$str.'';
                            },
                            'format' => 'raw',
                            'contentOptions' => ['class' => 'text-left'],
                        ],

                        //'created',
                        //'updated',

                        [
                            'attribute' => 'created',
                            'value' => function(\common\models\Client $model) {
                                return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                            },
                            'format' => 'html',
                        ],
                        [
                            'attribute' => 'updated',
                            'value' => function(\common\models\Client $model) {
                                return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated));
                            },
                            'format' => 'html',
                        ],
                    ],
                ]) ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
$addEmailAjaxUrl = \yii\helpers\Url::to(['cases/add-email', 'gid' => $caseModel->cs_gid]);
$clientUpdateAjaxUrl = \yii\helpers\Url::to(['cases/client-update', 'gid' => $caseModel->cs_gid]);

$js = <<<JS
     $(document).on('click', '#btn-add-email', function(){
            var modal = $('#modalCaseSm');
            //$('#search-sale-panel').toggle();
            modal.modal('show').find('.modal-body').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
            modal.modal('show').find('.modal-header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
            
            $.get('$addEmailAjaxUrl', function(data) {
                modal.find('.modal-body').html(data);
            });
            
           return false;
     });

    $(document).on('click', '#btn-client-update', function(){
            var modal = $('#modalCaseSm');
            //$('#search-sale-panel').toggle();
            modal.modal('show').find('.modal-body').html('<div style="text-align:center"><img width="200px" src="https://loading.io/spinners/gear-set/index.triple-gears-loading-icon.svg"></div>');
            modal.modal('show').find('.modal-header').html('<h3>' + $(this).attr('title') + ' ' + '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button></h3>');
            
            $.get('$clientUpdateAjaxUrl', function(data) {
                modal.find('.modal-body').html(data);
            });
            
           return false;
     });

JS;

$this->registerJs($js);
