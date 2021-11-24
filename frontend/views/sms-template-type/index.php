<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
use common\components\grid\UserSelect2Column;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SmsTemplateTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sms Template Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-template-type-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(['scrollTo' => 0]); ?>
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
                'label' => 'Department (deprecated)',
                'attribute' => 'stp_dep_id',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    return $model->stpDep ? $model->stpDep->dep_name : '-';
                },
                'filter' => false
            ],

            [
                'label' => 'Departments',
                'attribute' => 'stp_dep_id',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    $valueArr = [];

                    foreach ($model->smsTemplateTypeDepartments as $item) {
                        $valueArr[] = Html::tag('div', Html::encode($item->sttdDepartment->dep_name), ['class' => 'label label-default']) ;
                    }

                    return $valueArr ? implode('<br>', $valueArr)  : '-';
                },
                'filter' => \common\models\Department::getList(),
                'format' => 'raw'
            ],

            [
                'label' => 'Projects',
                'attribute' => 'projectId',
                'value' => static function (\common\models\SmsTemplateType $model) {
                    $valueArr = [];

                    foreach ($model->smsTemplateTypeProjects as $item) {
                        $valueArr[] = Html::tag('div', Html::encode($item->sttpProject->name), ['class' => 'label label-info']) ;
                    }

                    return $valueArr ? implode('<br>', $valueArr)  : '-';
                },
                'filter' => \common\models\Project::getList(),
                'format' => 'raw'
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'stp_updated_user_id',
                'relation' => 'stpUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'stp_updated_dt'
            ],

            /*[
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
            ],*/

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'stp_created_user_id',
                'relation' => 'stpCreatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'stp_created_dt'
            ],

            /*[
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
            ],*/

            //'stp_created_user_id',
            //'stp_updated_user_id',
            //'stp_created_dt',
            //'stp_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
