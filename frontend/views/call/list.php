<?php

use dosamigos\datepicker\DatePicker;
use common\components\grid\call\CallDurationColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap4\Modal;
use src\helpers\phone\MaskPhoneHelper;

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
        <?php //= Html::a('Create Call', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-check"></i> Make View All', ['all-read'], [
            'class' => 'btn btn-info',
            'data' => [
                'confirm' => 'Are you sure you want to mark view all Calls?',
                'method' => 'post',
            ],
        ]) ?>

        <?php /*= Html::a('<i class="fa fa-times"></i> Delete All', ['all-delete'], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete all SMS?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p>

    <?php /*
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
                'value' => static function (\common\models\Call $model) {
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
                'value' => static function (\common\models\Call $model) {
                    return $model->getCallTypeName();
                },
                'filter' => \common\models\Call::TYPE_LIST
            ],

            [
                'attribute' => 'c_source_type_id',
                'value' => static function (\common\models\Call $model) {
                    return $model->getSourceName();
                },
                'filter' => \common\models\Call::SOURCE_LIST
            ],

            [
                'class' => \common\components\grid\project\ProjectColumn::class,
                'attribute' => 'c_project_id',
                'relation' => 'cProject',
            ],

            //'c_from',
            [
                'attribute' => 'c_from',
                'value' => static function (\common\models\Call $model) {
                    if ($model->c_call_type_id == $model::CALL_TYPE_IN) {
                        return MaskPhoneHelper::masking($model->c_from);
                    }
                    return $model->c_from;
                }
            ],
            //'c_to',
            [
                'attribute' => 'c_to',
                'value' => static function (\common\models\Call $model) {
                    if ($model->c_call_type_id == $model::CALL_TYPE_OUT) {
                        return MaskPhoneHelper::masking($model->c_to);
                    }
                    return $model->c_to;
                }
            ],

            //'c_call_status',
            [
                'attribute' => 'c_status_id',
                'value' => static function (\common\models\Call $model) {
                    return $model->getStatusLabel();
                },
                'format' => 'raw',
                'filter' => \common\models\Call::STATUS_LIST
            ],
            //'c_lead_id',
            [
                'attribute' => 'c_lead_id',
                'value' => static function (\common\models\Call $model) {
                    return  $model->c_lead_id && $model->cLead->employee_id == Yii::$app->user->id ? Html::a($model->c_lead_id, ['lead/view', 'gid' => $model->cLead->gid], ['target' => '_blank', 'data-pjax' => 0]) : ($model->c_lead_id ?: '-');
                },
                'format' => 'raw'
            ],

            [
                'attribute' => 'c_case_id',
                'value' => static function (\common\models\Call $model) {
                    return  $model->c_case_id && $model->cCase->cs_user_id == Yii::$app->user->id ? Html::a($model->c_lead_id, ['cases/view', 'gid' => $model->cCase->cs_gid], ['target' => '_blank', 'data-pjax' => 0]) : ($model->c_case_id ?: '-');
                },
                'format' => 'raw'
            ],

            /*[
                'attribute' => 'c_client_id',
                'value' => static function (\common\models\Call $model) {
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
                'value' => static function (\common\models\Call $model) {
                    return  $model->c_recording_url ? '<audio controls="controls" style="width: 350px; height: 25px"><source src="'.$model->c_recording_url.'" type="audio/mpeg"> </audio>' : '-';
                },
                'format' => 'raw'
            ],*/

            ['class' => CallDurationColumn::class],

//            'c_recording_duration',
            //'c_sequence_number',

            //'c_created_user_id',

            /*[
                'attribute' => 'c_created_user_id',
                'value' => static function (\common\models\Call $model) {
                    return  $model->cCreatedUser ? '<i class="fa fa-user"></i> ' . Html::encode($model->cCreatedUser->username) : $model->c_created_user_id;
                },
                'format' => 'raw'
            ],*/

            //'c_created_dt',

            /*[
                'attribute' => 'c_updated_dt',
                'value' => static function (\common\models\Call $model) {
                    return $model->c_updated_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->c_updated_dt)) : '-';
                },
                'format' => 'raw'
            ],*/

            [
                'attribute' => 'c_created_dt',
                'value' => static function (\common\models\Call $model) {
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
                        'placeholder' => 'Choose Date'
                    ],
                ]),
            ],

            //'c_updated_dt',
            //'c_error_message',

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