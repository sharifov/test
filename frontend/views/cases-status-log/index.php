<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use sales\entities\cases\CasesStatusHelper;
use sales\entities\cases\CasesStatusLog;
use common\models\Employee;

/* @var $this yii\web\View */
/* @var $searchModel sales\entities\cases\CasesStatusLogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Case Status History';
$this->params['breadcrumbs'][] = $this->title;

$userList = Employee::getList();

?>
<div class="cases-status-log-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute' => 'csl_id',
                'options' => ['style' => 'width:100px'],
            ],
            [
                'attribute' => 'csl_from_status',
                'value' => function (CasesStatusLog $model) {
                    return '<span class="label label-info">' . CasesStatusHelper::getName($model->csl_from_status) . '</span></h5>';
                },
                'format' => 'raw',
                'filter' => CasesStatusHelper::STATUS_LIST,
                'options' => ['style' => 'width:180px'],
            ],
            [
                'attribute' => 'csl_to_status',
                'value' => function (CasesStatusLog $model) {
                    return '<span class="label label-info">' . CasesStatusHelper::getName($model->csl_to_status) . '</span></h5>';
                },
                'format' => 'raw',
                'filter' => CasesStatusHelper::STATUS_LIST,
                'options' => ['style' => 'width:180px'],
            ],
            [
                'attribute' => 'csl_case_id',
                'options' => ['style' => 'width:140px'],
            ],
            [
                'label' => 'Status start date',
                'attribute' => 'csl_start_dt',
                'value' => function (CasesStatusLog $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->csl_start_dt));
                },
                'format' => 'raw',
                'options' => ['style' => 'width:180px'],
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'csl_start_dt',
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
            [
                'label' => 'Status end date',
                'attribute' => 'csl_end_dt',
                'value' => function (CasesStatusLog $model) {
                    return $model->csl_end_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->csl_end_dt)) : '';
                },
                'format' => 'raw',
                'options' => ['style' => 'width:180px'],
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'csl_end_dt',
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
            'csl_time_duration',
            [
                'attribute' => 'csl_owner_id',
                'value' => function (CasesStatusLog $model) {
                    return $model->owner ? $model->owner->username : '';
                },
                'filter' => $userList
            ],
            [
                'attribute' => 'csl_created_user_id',
                'value' => function (CasesStatusLog $model) {
                    return $model->createdUser ? $model->createdUser->username : '';
                },
                'filter' => $userList
            ],
        ],
    ]); ?>


</div>
