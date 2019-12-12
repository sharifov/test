<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SmsTemplateTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sms Template Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-template-type-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Sms Template Type', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-refresh"></i> Synchronization Email Template Types from Communication', ['synchronization '], ['class' => 'btn btn-warning', 'data' => [
            'confirm' => 'Are you sure you want synchronization all Email Template Types from Communication Services?',
            'method' => 'post',
        ],]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function (\common\models\SmsTemplateType $model) {
            if ($model->stp_hidden) {
                return ['class' => 'danger'];
            }
            return [];
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'stp_id',
            'stp_key',
            'stp_origin_name',
            'stp_name',
            'stp_hidden:boolean',
            [
                'attribute' => 'stp_dep_id',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    return $model->stpDep ? $model->stpDep->dep_name : '-';
                },
                'filter' => \common\models\Department::getList()
            ],

            [
                'attribute' => 'stp_updated_user_id',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    return ($model->stpUpdatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->stpUpdatedUser->username) : $model->stp_updated_user_id);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'stp_updated_dt',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->stp_updated_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'stp_updated_dt',
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

            [
                'attribute' => 'stp_created_user_id',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    return  ($model->stpCreatedUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->stpCreatedUser->username) : $model->stp_created_user_id);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'stp_created_dt',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->stp_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'stp_created_dt',
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
            
            //'stp_created_user_id',
            //'stp_updated_user_id',
            //'stp_created_dt',
            //'stp_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
