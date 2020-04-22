<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
use common\components\grid\UserSelect2Column;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmailTemplateTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Email Template Types';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="email-template-type-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('<i class="fa fa-plus"></i> Create Email Template Type', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('<i class="fa fa-refresh"></i> Synchronization Email Template Types from Communication', ['synchronization '], ['class' => 'btn btn-warning', 'data' => [
            'confirm' => 'Are you sure you want synchronization all Email Template Types from Communication Services?',
            'method' => 'post',
        ],]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function (\common\models\EmailTemplateType $model) {
            if ($model->etp_hidden) {
                return ['class' => 'danger'];
            }
            return [];
        },
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'etp_id',
            'etp_key',
            'etp_origin_name',
            'etp_name',
            'etp_hidden:boolean',

            [
                'attribute' => 'etp_dep_id',
                'value' => static function (\common\models\EmailTemplateType $model) {
                    return $model->etpDep ? $model->etpDep->dep_name : '-';
                },
                'filter' => \common\models\Department::getList()
            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'etp_updated_user_id',
                'relation' => 'etpUpdatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'attribute' => 'etp_updated_dt',
                'value' => static function (\common\models\EmailTemplateType $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->etp_updated_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'etp_updated_dt',
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
                'class' => UserSelect2Column::class,
                'attribute' => 'etp_created_user_id',
                'relation' => 'etpCreatedUser',
                'placeholder' => 'Select User',
            ],

            [
                'attribute' => 'etp_created_dt',
                'value' => static function (\common\models\EmailTemplateType $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->etp_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'etp_created_dt',
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

            /*'etp_created_user_id',
            'etp_updated_user_id',
            'etp_created_dt',
            'etp_updated_dt',*/

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
