<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $phoneList [] */
/* @var $projectList [] */

$this->title = 'My Calls';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="call-index">

    <h1><i class="fa fa-phone"></i> <?= Html::encode($this->title) ?></h1>





    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?//= Html::a('Create Call', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-check"></i> Make View All', ['all-read'], [
            'class' => 'btn btn-info',
            'data' => [
                'confirm' => 'Are you sure you want to mark view all Calls?',
                'method' => 'post',
            ],
        ]) ?>

        <?/*= Html::a('<i class="fa fa-times"></i> Delete All', ['all-delete'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete all SMS?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <?/*
    <div class="row top_tiles">

        <div class="animated flipInY col-md-3 col-sm-6 col-xs-12">
            <h5>My Phone List (<?=count($phoneList)?>):</h5>
            <table class="table table-bordered table-striped">
                <tr>
                    <th>Nr</th>
                    <th>Phone</th>
                </tr>
                <?php
                $nr = 1;
                foreach ($phoneList as $phone):?>
                    <tr>
                        <td width="100px"><?=($nr++)?></td>
                        <td><?=Html::encode($phone)?></td>
                    </tr>
                <?php endforeach; ?>

            </table>


        </div>

        <div class="animated flipInY col-md-3 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-phone"></i></div>
                <div class="count">
                    <?=\common\models\Call::find()->where(['or', ['c_to' => $phoneList], ['c_from' => $phoneList], ['c_created_user_id' => Yii::$app->user->id]])
                        ->andWhere(['c_is_new' => true, 'c_is_deleted' => false])->count()?>
                </div>
                <h3>New Calls</h3>
                <p>Total new Calls</p>
            </div>
        </div>

        <div class="animated flipInY col-md-3 col-sm-6 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-phone"></i></div>
                <div class="count">
                    <?=\common\models\Call::find()->where(['or', ['c_to' => $phoneList], ['c_from' => $phoneList], ['c_created_user_id' => Yii::$app->user->id]])
                        ->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_IN, 'DATE(c_created_dt)' => new \yii\db\Expression('DATE(NOW())')])->count()?>
                </div>
                <h3>Today Incoming Calls</h3>
                <p>Today count of incoming Calls</p>
            </div>
        </div>

        <div class="animated flipInY col-md-3 col-sm-2 col-xs-12">
            <div class="tile-stats">
                <div class="icon"><i class="fa fa-phone"></i></div>
                <div class="count">
                    <?=\common\models\Call::find()->where(['or', ['c_to' => $phoneList], ['c_from' => $phoneList], ['c_created_user_id' => Yii::$app->user->id]])
                        ->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_OUT, 'DATE(c_created_dt)' => new \yii\db\Expression('DATE(NOW())')])->count()?>
                </div>
                <h3>Today Outgoing Calls</h3>
                <p>Today count of outgoing Calls</p>
            </div>
        </div>

    </div>
*/?>

    <?php Pjax::begin(['timeout' => 10000]); ?>

    <?php echo $this->render('_my_call_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => function (\common\models\Call $model, $index, $widget, $grid) {
            if ($model->isStatusBusy() || $model->isStatusNoAnswer()) {
                return ['class' => 'danger'];
            } elseif ($model->isStatusRinging() || $model->isStatusQueue()) {
                return ['class' => 'warning'];
            } elseif ($model->isStatusCompleted()) {
                // return ['class' => 'success'];
            }
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'s_is_deleted',

            [
                'attribute' => 'c_id',
                'value' => function (\common\models\Call $model) {
                    return $model->c_id;
                },
                'options' => ['style' => 'width: 100px']
            ],

            'c_is_new:boolean',
            //'c_com_call_id',
            //'c_call_sid',
            //'c_call_type_id',

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

            [
                'attribute' => 'c_project_id',
                'value' => function (\common\models\Call $model) {
                    return $model->cProject ? $model->cProject->name : '-';
                },
                'filter' => $projectList
            ],


            'c_from',
            'c_to',

            //'c_call_status',
            [
                'attribute' => 'c_status_id',
                'value' => function (\common\models\Call $model) {
                    return $model->getStatusLabel();
                },
                'format' => 'raw',
                'filter' => \common\models\Call::STATUS_LIST
            ],
            //'c_lead_id',
            [
                'attribute' => 'c_lead_id',
                'value' => function (\common\models\Call $model) {
                    return  $model->c_lead_id && $model->cLead->employee_id == Yii::$app->user->id ? Html::a($model->c_lead_id, ['lead/view', 'gid' => $model->cLead->gid], ['target' => '_blank', 'data-pjax' => 0]) : $model->c_lead_id ?: '-';
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'c_case_id',
                'value' => function (\common\models\Call $model) {
                    return  $model->c_case_id && $model->cCase->cs_user_id == Yii::$app->user->id ? Html::a($model->c_lead_id, ['cases/view', 'gid' => $model->cCase->cs_gid], ['target' => '_blank', 'data-pjax' => 0]) : $model->c_case_id ?: '-';
                },
                'format' => 'raw'
            ],

            /*[
                'attribute' => 'c_client_id',
                'value' => function (\common\models\Call $model) {
                    return  $model->c_client_id ?: '-';
                },
                //'format' => 'raw'
            ],*/

            //'c_forwarded_from',
            //'c_caller_name',
            //'c_parent_call_sid',
            'c_call_duration',
            //'c_recording_url:url',
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

            'c_recording_duration',
            //'c_sequence_number',

            //'c_created_user_id',

            /*[
                'attribute' => 'c_created_user_id',
                'value' => function (\common\models\Call $model) {
                    return  $model->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cCreatedUser->username) : $model->c_created_user_id;
                },
                'format' => 'raw'
            ],*/

            //'c_created_dt',

            /*[
                'attribute' => 'c_updated_dt',
                'value' => function (\common\models\Call $model) {
                    return $model->c_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],*/

            [
                'attribute' => 'c_created_dt',
                'value' => function (\common\models\Call $model) {
                    return $model->c_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_created_dt)) : '-';
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
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],

            //'c_updated_dt',
            //'c_error_message',
            //'c_is_deleted:boolean',

            [   'class' => 'yii\grid\ActionColumn',
                'template' => '{view2}',
                'buttons' => [
                    'view2' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-search"></i>', $url, [
                            'title' => 'View',
                        ]);
                    },
                    /*'soft-delete' => function ($url, $model) {
                        return Html::a('<i class="glyphicon glyphicon-remove-circle"></i>', $url, [
                            'title' => 'Delete',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this SMS?',
                                //'method' => 'post',
                            ],
                        ]);
                    }*/
                ],
            ],
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
     $('#audio_recording').html('<audio controls="controls" controlsList="nodownload" autoplay="true" id="audio_controls" style="width: 100%;"><source src="'+ source_src +'" type="audio/mpeg"></audio>');
     $('#modalCallRecording').modal('show');
});

$('#modalCallRecording').on('hidden.bs.modal', function () {
    $('#audio_recording').html('');
});

JS;
$this->registerJs($js, \yii\web\View::POS_READY);
?>