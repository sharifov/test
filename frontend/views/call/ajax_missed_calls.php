<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CallSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="ajax-call-index">



    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php Pjax::begin(['id' => 'pjax-missed-calls']); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null, //$searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => function (\common\models\Call $model, $index, $widget, $grid) {
            if ($model->c_is_new) {
                return ['class' => 'danger'];
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
                'enableSorting' => false,
                'options' => ['style' => 'width: 80px']
            ],

            'c_is_new:boolean',
            //'c_com_call_id',
            //'c_call_sid',
            //'c_call_type_id',

            /*[
                'attribute' => 'c_call_type_id',
                'value' => function (\common\models\Call $model) {
                    return $model->getCallTypeName();
                },
                'filter' => \common\models\Call::CALL_TYPE_LIST
            ],*/


            //'c_project_id',

            [
                'attribute' => 'c_project_id',
                'value' => function (\common\models\Call $model) {
                    return $model->cProject ? $model->cProject->name : '-';
                },
                'enableSorting' => false
                //'filter' => $projectList
            ],



            [
                'attribute' => 'c_from',
                'enableSorting' => false,
                'filter' => false //\common\models\Call::CALL_STATUS_LIST
            ],
            //'c_to',

            //'c_call_status',
            [
                'attribute' => 'c_call_status',
                'value' => function (\common\models\Call $model) {
                    return $model->getStatusLabel();
                },
                'enableSorting' => false,
                'format' => 'raw',
                'filter' => false //\common\models\Call::CALL_STATUS_LIST
            ],
            //'c_lead_id',
            [
                'attribute' => 'c_lead_id',
                'value' => function (\common\models\Call $model) {
                    return  $model->c_lead_id ? Html::a($model->c_lead_id, ['lead/view', 'gid' => $model->cLead->gid], ['target' => '_blank', 'data-pjax' => 0]) : '-';
                },
                'format' => 'raw',
                'enableSorting' => false
            ],
            //'c_forwarded_from',
            //'c_caller_name',
            //'c_parent_call_sid',
            //'c_call_duration',
            //'c_recording_url:url',
            /*[
                'attribute' => 'c_recording_url',
                'value' => function (\common\models\Call $model) {
                    return  $model->c_recording_url ? '<audio controls="controls" style="width: 350px; height: 25px"><source src="'.$model->c_recording_url.'" type="audio/mpeg"> </audio>' : '-';
                },
                'format' => 'raw'
            ],*/

            //'c_recording_duration',
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
                'enableSorting' => false
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
                            'target' => '_blank',
                            'data-pjax' => 0
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