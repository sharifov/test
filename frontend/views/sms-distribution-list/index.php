<?php

use frontend\widgets\multipleUpdate\button\MultipleUpdateButtonWidget;
use src\model\sms\entity\smsDistributionList\SmsDistributionList;
use common\components\grid\CombinedDataColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\UserSelect2Column;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\Pjax;
use src\helpers\phone\MaskPhoneHelper;

/* @var $this yii\web\View */
/* @var $searchModel src\model\sms\entity\smsDistributionList\search\SmsDistributionListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sms Distribution List';
$this->params['breadcrumbs'][] = $this->title;
$user = Yii::$app->user->identity;
$gridId = 'sms-grid-id';
?>
<div class="sms-distribution-list-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Sms Distribution', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-plus"></i> Add Multiple Sms', ['create-multiple'], ['class' => 'btn btn-warning']) ?>
        <?= Html::a('<i class="fa fa-trash"></i> Delete All', ['delete-all'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete all items?',
                //'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="col-md-12">
        Site settings "<b>sms_distribution_count</b>":  <b><?=(Yii::$app->params['settings']['sms_distribution_count'] ?? 'none')?></b>
        <div class="text-info"> SMS are sent only in the status of Pending. With a frequency of once every 2 minutes. </div>
    </div>


    <?php Pjax::begin(['id' => 'pjax-sms-grid-list', 'scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php //=Html::beginForm(['/sms-distribution-list/update-multiple'],'post', ['data-pjax' => true])?>
    <h5>Multiple Update - selected items</h5>
        <div class="col-md-1">
            <?=Html::dropDownList('SmsMultipleForm[status_id]', '', SmsDistributionList::getStatusList(), ['id' => 'status_id', 'class' => 'form-control', 'prompt' => '-'])?>
        </div>
        <div class="col-md-2">
            <?=Html::button('<i class="fa fa-save"></i> Update selected items', ['class' => 'btn btn-warning btn-submit-multiple-update']);?>
        </div>

    <?= GridView::widget([
        'id' => 'sms-list-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'class' => 'yii\grid\CheckboxColumn',
                /*'name' => 'SmsMultipleForm[sms_list]',
                'checkboxOptions' => static function(SmsDistributionList $model) {
                    return ['value' => $model->sdl_id];
                },*/
                //'pageSummary' => true,
                //'rowSelectedClass' => ,
                /*'checkboxOptions' => static function (SmsDistributionList $model) {
                    $can = Auth::can('leadSearchMultipleUpdate', ['lead' => $model]);
                    return ['style' => 'display:' . ($can ? 'visible' : 'none')];
                },
                'visible' => Auth::can('leadSearchMultipleSelect')*/
            ],

            'sdl_id',
//            'sdl_project_id',
//            [
//                'attribute' => 'sdl_project_id',
//                'value' => static function (SmsDistributionList $model) {
//                    return $model->sdlProject ? '<span class="label label-default">' . Html::encode($model->sdlProject->name) . '</span>' : '';
//                },
//                'format' => 'raw',
//                'filter' => \common\models\Project::getList()
//            ],
            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'sdl_project_id',
                'relation' => 'sdlProject',
            ],
            [
                'attribute' => 'sdl_status_id',
                'value' => static function (SmsDistributionList $model) {
                    $text = $model->getStatusLabel();
                    if ((int) $model->sdl_status_id === SmsDistributionList::STATUS_ERROR) {
                        $text .= ' <span class="fa fa-info" title="' . Html::encode($model->sdl_error_message) . '"></span>';
                    }
                    return $text;
                },
                'format' => 'raw',
                'filter' => SmsDistributionList::getStatusList()
            ],
            'sdl_phone_from',
            //'sdl_phone_to',
            [
                'attribute' => 'sdl_phone_to',
                'value' => static function (SmsDistributionList $model) {

                    return MaskPhoneHelper::masking($model->sdl_phone_to);
                }
            ],
            'sdl_client_id:client',
            /*[
                'label' => 'Client Name',
                'value' => static function (SmsDistributionList $model) {
                    return $model->sdl_client_id && $model->sdlClient ? $model->sdlClient->full_name : '-';
                },
            ],*/
            [
                'attribute' => 'sdl_text',
                'value' => static function (SmsDistributionList $model) {
                    return '<pre><small>' . nl2br(Html::encode($model->sdl_text)) . '</small></pre>';
                },
                'format' => 'raw',

            ],
            //'sdl_text:ntext',
            'sdl_start_dt',
            'sdl_end_dt',
            //'sdl_status_id',
            'sdl_priority',
            //'sdl_num_segments',
            //'sdl_price',
            //'sdl_error_message:ntext',
            //'sdl_message_sid',
            //'sdl_created_user_id',
            //'sdl_updated_user_id',
//            [
//                'class' => DateTimeColumn::class,
//                'attribute' => 'sdl_created_dt',
//            ],
//            [
//                'class' => DateTimeColumn::class,
//                'attribute' => 'sdl_updated_dt',
//            ],


            [
                'class' => CombinedDataColumn::class,
                'labelTemplate' => '{0}  /  {1}',
                'valueTemplate' => '{0}  <br>  {1}',
//                'labels' => [
//                    'Created At',
//                    '[ Updated At ]',
//                ],
                'attributes' => [
                    'sdl_created_dt:byUserDateTime',
                    'sdl_updated_dt:byUserDateTime',
                ],
//                'values' => [
//                    null,
//                    function ($model, $_key, $_index, $_column) {
//                        return '[ ' . Yii::$app->formatter->asDatetime($model->updated_at) . ' ]';
//                    },
//                ],
//                'sortLinksOptions' => [
//                    ['class' => 'text-nowrap'],
//                    null,
//                ],
            ],



//            [
//                'label' => 'Created User',
//                'attribute' => 'sdlCreatedUser.username',
//            ],

//            [
//                'class' => \src\yii\grid\UserColumn::class,
//                'attribute' => 'sdl_updated_user_id',
//                'relation' => 'sdlUpdatedUser'
//            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'sdl_created_user_id',
                'relation' => 'sdlCreatedUser',
                'placeholder' => 'Select User'
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'sdl_updated_user_id',
                'relation' => 'sdlUpdatedUser',
                'placeholder' => 'Select User'
            ],

            'sdl_com_id',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete} {send}',
                'visibleButtons' => [
                    /*'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },*/
                    'update' => static function ($model, $key, $index) use ($user) {
                        return $user->isAdmin();
                    },

                    'delete' => static function ($model, $key, $index) use ($user) {
                        return $user->isAdmin();
                    },

                    'send' => static function (SmsDistributionList $model, $key, $index) use ($user) {
                        return $user->isAdmin() && (int) $model->sdl_status_id === SmsDistributionList::STATUS_PENDING;
                    },
                ],
                'buttons' => [
                    'send' => static function ($url, SmsDistributionList $model) {
                        return Html::a('<i class="fa fa-send text-warning"></i>', ['send', 'id' => $model->sdl_id], [
                            //'class' => 'btn btn-primary btn-xs take-processing-btn',
                            'title' => 'Send SMS',
                            'data-pjax' => 0,
                            'data' => [
                                'confirm' => 'Are you sure you want send this SMS?',
                                //'method' => 'post',
                            ],
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>


    <?php //= Html::endForm();?>

    <?php Pjax::end(); ?>

</div>

<?php
$urlAjax = Url::to(['sms-distribution-list/update-multiple']);
$js = <<<JS

    $(document).ready(function () {
        $(document).on('click', '.btn-submit-multiple-update', function() {
            //alert(123);
            let ids = $('#sms-list-grid').yiiGridView('getSelectedRows');
            if (ids.length < 1) {
                createNotifyByObject({title: "Multiple update", type: "error", text: 'Not selected rows', hide: true});
                return;
            }
            
            let status_id = $('#status_id').val();
            
            if (!status_id) {
                createNotifyByObject({title: "Select Status", type: "error", text: 'Not select status', hide: true});
                return;
            }
            
            if (!confirm('Are you sure you want to update selected items?')) {
                return;
            }
            
            $('#preloader').removeClass('hidden');
            $.ajax({
                type: 'post',
                url: '$urlAjax',
                data: {"sms_list": ids, "status_id" : status_id},
                success: function (data) {
                    $('#preloader').addClass('hidden');
                    createNotifyByObject({title: "Multiple update Success", type: "success", text: 'Multiple update Success', hide: true});
                    $.pjax.reload({container: '#pjax-sms-grid-list', async: false});
                },
                error: function (error) {
                    $('#preloader').addClass('hidden');
                    console.error('Error: ' + error);
                }
            });

        });
       
      
        
    }); 

    // $(document).on('pjax:start', function() {
    //     $("#modalUpdate .close").click();
    // });

    /*$(document).on('pjax:end', function() {
         $('[data-toggle="tooltip"]').tooltip();
    });*/

JS;

$this->registerJs($js, \yii\web\View::POS_READY);