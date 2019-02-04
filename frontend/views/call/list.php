<?php

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
                <? endforeach; ?>

            </table>

            <?/*<div class="tile-stats">
                <div class="icon"><i class="fa fa-comments"></i></div>
                <div class="count">

                    <?=\common\models\Sms::find()->where(['or', ['s_phone_to' => $phoneList], ['s_phone_from' => $phoneList]])
                        ->andWhere(['s_type_id' => \common\models\Sms::FILTER_TYPE_DRAFT, 's_is_deleted' => false])->count()?>
                </div>
                <h3>Draft</h3>
                <p>Draft count of SMS messages</p>
            </div>*/ ?>
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
                        ->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_IN, 'DATE(c_created_dt)' => 'DATE(NOW())'])->count()?>
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
                        ->andWhere(['c_call_type_id' => \common\models\Call::CALL_TYPE_OUT, 'DATE(c_created_dt)' => 'DATE(NOW())'])->count()?>
                </div>
                <h3>Today Outgoing Calls</h3>
                <p>Today count of outgoing Calls</p>
            </div>
        </div>

        <?php /*
            <div class="animated flipInY col-lg-2 col-md-2 col-sm-6 col-xs-12">
                <div class="tile-stats">
                    <div class="icon"><i class="fa fa-list"></i></div>
                    <div class="count"><?=\frontend\models\Log::find()->where("log_time BETWEEN ".strtotime(date('Y-m-d'))." AND ".strtotime(date('Y-m-d H:i:s')))->count()?></div>
                    <h3>System Logs</h3>
                    <p>Today count of System Logs</p>
                </div>
            </div>
            */ ?>
    </div>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered table-condensed table-hover'],
        'rowOptions' => function (\common\models\Call $model, $index, $widget, $grid) {
            if ($model->c_call_status === \common\models\Call::CALL_STATUS_BUSY) {
                return ['class' => 'danger'];
            } elseif ($model->c_call_status === \common\models\Call::CALL_STATUS_RINGING || $model->c_call_status === \common\models\Call::CALL_STATUS_QUEUE) {
                return ['class' => 'warning'];
            } elseif ($model->c_call_status === \common\models\Call::CALL_STATUS_COMPLETED) {
                return ['class' => 'success'];
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
            //'c_account_sid',
            //'c_call_type_id',

            [
                'attribute' => 'c_call_type_id',
                'value' => function (\common\models\Call $model) {
                    return $model->getCallTypeName();
                },
                'filter' => \common\models\Call::CALL_TYPE_LIST
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

            //'c_sip',
            //'c_call_status',
            [
                'attribute' => 'c_call_status',
                'value' => function (\common\models\Call $model) {
                    return $model->c_call_status;
                },
                'filter' => \common\models\Call::CALL_STATUS_LIST
            ],
            //'c_lead_id',
            [
                'attribute' => 'c_lead_id',
                'value' => function (\common\models\Call $model) {
                    return  $model->c_lead_id ? Html::a($model->c_lead_id, ['lead/view', 'id' => $model->c_lead_id, ['target' => '_blank', 'data-pjax' => 0]]) : '-';
                },
                'format' => 'raw'
            ],
            //'c_api_version',
            //'c_direction',
            //'c_forwarded_from',
            'c_caller_name',
            //'c_parent_call_sid',
            'c_call_duration',
            //'c_sip_response_code',
            //'c_recording_url:url',
            [
                'attribute' => 'c_recording_url',
                'value' => function (\common\models\Call $model) {
                    return  $model->c_recording_url ? '<audio controls="controls" style="width: 350px; height: 25px"><source src="'.$model->c_recording_url.'" type="audio/mpeg"> </audio>' : '-';
                },
                'format' => 'raw'
            ],
            //'c_recording_sid',
            'c_recording_duration',
            //'c_timestamp',
            //'c_uri',
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
                'format' => 'raw'
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
