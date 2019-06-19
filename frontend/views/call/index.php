<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Calls';
$this->params['breadcrumbs'][] = $this->title;

if(Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id) || Yii::$app->authManager->getAssignment('qa', Yii::$app->user->id)) {
    $userList = \common\models\Employee::getList();
    $projectList = \common\models\Project::getList();
} else {
    $userList = \common\models\Employee::getListByUserId(Yii::$app->user->id);
    $projectList = \common\models\Project::getListByUser(Yii::$app->user->id);
}

?>
<div class="call-index">
    <h1><i class="fa fa-phone"></i> <?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="row">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => [
                'data-pjax' => 1
            ],
        ]); ?>

        <div class="col-md-3">
            <?php
            echo  \kartik\daterange\DateRangePicker::widget([
                'model'=> $searchModel,
                'attribute' => 'date_range',
                'useWithAddon'=>true,
                'presetDropdown'=>true,
                'hideInput'=>true,
                'convertFormat'=>true,
                'startAttribute' => 'datetime_start',
                'endAttribute' => 'datetime_end',
                'pluginOptions'=>[
                    'timePicker'=> false,
                    'timePickerIncrement'=>15,
                    'locale'=>[
                        'format'=>'Y-m-d',
                        'separator' => ' - '
                    ]
                ]
            ]);
            ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-search"></i> Show result', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <p>
        <?/*= Html::a('Create Call', ['create'], ['class' => 'btn btn-success'])*/ ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => function (\common\models\Call $model, $index, $widget, $grid) {
            if($model->c_call_type_id == \common\models\Call::CALL_TYPE_OUT) {
                if ($model->c_call_status === \common\models\Call::CALL_STATUS_BUSY || $model->c_call_status === \common\models\Call::CALL_STATUS_NO_ANSWER) {
                    return ['class' => 'danger'];
                } elseif ($model->c_call_status === \common\models\Call::CALL_STATUS_RINGING || $model->c_call_status === \common\models\Call::CALL_STATUS_QUEUE) {
                    return ['class' => 'warning'];
                } elseif ($model->c_call_status === \common\models\Call::CALL_STATUS_COMPLETED) {
                    // return ['class' => 'success'];
                }
            }
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'c_id',
                'value' => function (\common\models\Call $model) {
                    return $model->c_id;
                },
                'options' => ['style' => 'width: 80px']
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    /*'view' => function ($model, $key, $index) {
                        return User::hasPermission('viewOrder');
                    },*/
                    'update' => function ($model, $key, $index) {
                        return Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id);
                    },

                    'delete' => function ($model, $key, $index) {
                        return Yii::$app->authManager->getAssignment('admin', Yii::$app->user->id);
                    },
                ],
            ],
            [
                'attribute' => 'c_project_id',
                'value' => function (\common\models\Call $model) {
                    return $model->cProject ? '<span class="badge badge-info">' . Html::encode($model->cProject->name) . '</span>' : '-';
                },
                'format' => 'raw',
                'filter' => $projectList
            ],
            [
                'attribute' => 'c_created_user_id',
                'value' => function (\common\models\Call $model) {
                    return  $model->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cCreatedUser->username) : $model->c_created_user_id;
                },
                'filter' => $userList,
                'format' => 'raw'
            ],

            [
                'attribute' => 'c_call_status',
                'value' => function (\common\models\Call $model) {
                    return $model->getStatusLabel();
                },
                'format' => 'raw',
                'filter' => \common\models\Call::CALL_STATUS_LIST
            ],

            [
                'attribute' => 'c_created_dt',
                'value' => function (\common\models\Call $model) {
                    return $model->c_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_created_dt), 'php: Y-m-d [H:i:s]')  : '-';  //Yii::$app->formatter->asDatetime(strtotime($model->c_created_dt))
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'c_created_dt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ],
                ]),
            ],

            /*[
                'attribute' => 'c_recording_url',
                'value' => function (\common\models\Call $model) {
                    return  $model->c_recording_url ? '<audio controls="controls" style="width: 350px; height: 25px"><source src="'.$model->c_recording_url.'" type="audio/mpeg"> </audio>' : '-';
                },
                'format' => 'raw'
            ],*/

            [
                'attribute' => 'c_recording_duration',
                'label' => 'Recording',
                'value' => function (\common\models\Call $model) {
                    return  $model->c_recording_url ? Html::button(gmdate('i:s', $model->c_recording_duration) . ' <i class="fa fa-volume-up"></i>', ['class' => 'btn btn-' . ($model->c_recording_duration < 30 ? 'warning' : 'success') . ' btn-xs btn-recording_url', 'data-source_src' => $model->c_recording_url]) : '-';
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-right'],
                'options' => ['style' => 'width: 80px']

            ],

            //'c_recording_duration',

            /*[
                'label' => 'Record Link',
                'value' => function (\common\models\Call $model) {
                    return  $model->c_recording_url ? Html::a('Link', $model->c_recording_url, ['target' => '_blank']) : '-';
                },
                'format' => 'raw'
            ],*/

            //'c_is_new:boolean',
            //'c_com_call_id',
            [
                'attribute' => 'c_call_sid',
                'value' => function (\common\models\Call $model) {
                    return  $model->c_call_sid ? '<small>' . $model->c_call_sid . '</small>' : '-';
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'c_parent_call_sid',
                'value' => function (\common\models\Call $model) {
                    return  $model->c_parent_call_sid ? '<small>' . $model->c_parent_call_sid . '</small>' : '-';
                },
                'format' => 'raw'
            ],
            //'c_call_sid',
            //'c_parent_call_sid',
            //'c_account_sid',

            [
                'attribute' => 'c_call_type_id',
                'value' => function (\common\models\Call $model) {
                    return $model->getCallTypeName();
                },
                'filter' => \common\models\Call::CALL_TYPE_LIST
            ],

            [
                'attribute' => 'c_source_type_id',
                'value' => function (\common\models\Call $model) {
                    return $model->getSourceName();
                },
                'filter' => \common\models\Call::SOURCE_LIST
            ],

            //'c_project_id',



            //'c_lead_id',
            [
                'attribute' => 'c_lead_id',
                'value' => function (\common\models\Call $model) {
                    return  $model->c_lead_id ? Html::a($model->c_lead_id, ['lead/view', 'gid' => $model->cLead->gid, ['target' => '_blank', 'data-pjax' => 0]]) : '-';
                },
                'format' => 'raw'
            ],
            'c_from',
            'c_to',
            // 'c_sip',
            //'c_call_status',



            //'c_api_version',
            //'c_direction',
            //'c_forwarded_from',
            //'c_caller_name',
            //'c_parent_call_sid',
            'c_call_duration',
            //'c_price:currency',
            /*[
                'attribute' => 'c_price',
                'value' => function (\common\models\Call $model) {
                    return $model->c_price ? '$'.number_format($model->c_price, 5) : '-';
                },
            ],*/
            //'c_sip_response_code',
            //'c_recording_url:url',



            //'c_recording_sid',

            //'c_timestamp',
            //'c_uri',
            //'c_sequence_number',

            //'c_created_user_id',



            //'c_created_dt',

            /*[
                'attribute' => 'c_updated_dt',
                'value' => function (\common\models\Call $model) {
                    return $model->c_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],*/




            //'c_updated_dt',

            //'c_error_message',

            //'c_is_deleted:boolean',


        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>

<?php
\yii\bootstrap\Modal::begin([
    'header' => '<b>Call Recording</b>',
    // 'toggleButton' => ['label' => 'click me'],
    'id' => 'modalCallRecording',
    'size' => \yii\bootstrap\Modal::SIZE_LARGE,
]);
?>
    <div class="row">
        <div class="col-md-12" id="audio_recording">

        </div>
    </div>
<?php \yii\bootstrap\Modal::end(); ?>


<?php

$js = <<<JS
$(document).on('click', '.btn-recording_url', function() {
     var source_src = $(this).data('source_src');
     $('#audio_recording').html('<audio controls="controls" autoplay="true" id="audio_controls" style="width: 100%;"><source src="'+ source_src +'" type="audio/mpeg"></audio>');
     $('#modalCallRecording').modal('show');
});

$('#modalCallRecording').on('hidden.bs.modal', function () {
    $('#audio_recording').html('');
})

JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>