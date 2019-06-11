<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserCallStatusSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Call Statuses';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-call-status-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?//= Html::a('Create User Call Status', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

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
            <?= Html::submitButton('<i class="fa fa-close"></i> Reset', ['name' => 'reset', 'class' => 'btn btn-warning']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'us_id',
            //'us_type_id',
            [
                'attribute' => 'us_type_id',
                'value' => function (\common\models\UserCallStatus $model) {
                    return $model->getStatusTypeName();
                },
                'format' => 'raw',
                'filter' => \common\models\UserCallStatus::STATUS_TYPE_LIST
            ],
            //'us_user_id',
            [
                'attribute' => 'us_user_id',
                'value' => function (\common\models\UserCallStatus $model) {
                    return ($model->usUser ? '<i class="fa fa-user"></i> ' .Html::encode($model->usUser->username) : $model->us_user_id);
                },
                'format' => 'raw',
                'filter' => \common\models\Employee::getList()
            ],

            [
                'attribute' => 'us_created_dt',
                'value' => function (\common\models\UserCallStatus $model) {
                    return $model->us_created_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->us_created_dt)) : $model->us_created_dt;
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'us_created_dt',
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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
