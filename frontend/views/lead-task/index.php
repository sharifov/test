<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LeadTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lead Tasks';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lead-task-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a('Create Lead Task', ['create'], ['class' => 'btn btn-success']) ?>
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

            'lt_lead_id',
            [
                'label' => 'Lead UID',
                'attribute' => 'lt_lead_id',
                'value' => function(\common\models\LeadTask $model) {
                    return Html::a($model->ltLead->uid, ['lead/view', 'gid' => $model->ltLead->gid], ['target' => '_blank', 'data-pjax' => 0]);
                },
                'format' => 'raw',
                'filter' => false
            ],

            [
                'label' => 'Task',
                'attribute' => 'lt_task_id',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->ltTask ? $model->ltTask->t_name : '-';
                },
                'filter' => \common\models\Task::getList()
            ],

            //'lt_task_id',
            //'ltTask.t_name',
            //'lt_user_id',
            //'ltUser.username',

            [
                'label' => 'Employee',
                'attribute' => 'lt_user_id',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->ltUser ? $model->ltUser->username : '-';
                },
                'filter' => \common\models\Employee::getList()
            ],

            //'lt_date:date',
            [
                'attribute' => 'lt_date',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->lt_date ? '<i class="fa fa-calendar"></i> '. date('d-M-Y', strtotime($model->lt_date))  : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'lt_date',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ],
                ]),
            ],
            'lt_notes',

            [
                'attribute' => 'lt_completed_dt',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->lt_completed_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->lt_completed_dt)) : '-';
                },
                'format' => 'raw',
            ],

            [
                'attribute' => 'lt_updated_dt',
                'value' => function(\common\models\LeadTask $model) {
                    return $model->lt_updated_dt ? '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->lt_updated_dt)) : '-';
                },
                'format' => 'raw',
            ],
            //'lt_completed_dt',
            //'lt_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
