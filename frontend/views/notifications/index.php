<?php

use common\components\grid\UserSelect2Column;
use dosamigos\datepicker\DatePicker;
use common\components\grid\DateTimeColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\NotificationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('notifications', 'Notifications');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notifications-index">
    <h1><i class="fa fa-bell-o"></i> <?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?= Html::a(Yii::t('notifications', 'Create Notifications'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>
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
                    'timePicker'=> true,
                    'timePickerIncrement'=>1,
                    'timePicker24Hour' => true,
                    'locale'=>[
                        'format'=>'Y-m-d H:i',
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

            'n_id',
            //'n_user_id',
//            [
//                'attribute' => 'n_user_id',
//                'value' => function(\common\models\Notifications $model){
//                    return $model->nUser->username;
//                },
//                'filter' => \common\models\Employee::getList()
//            ],

            [
                'class' => UserSelect2Column::class,
                'attribute' => 'n_user_id',
                'relation' => 'nUser',
                'placeholder' => ''
            ],

            [
                'attribute' => 'n_type_id',
                'value' => function(\common\models\Notifications $model){
                    return '<span class="label label-default">'.$model->getType().'</span>';
                },
                'format' => 'raw',
                'filter' => \common\models\Notifications::getTypeList()
            ],
            'n_title',
            'n_message:ntextWithPurify',
            'n_new:boolean',
            'n_deleted:boolean',
            'n_popup:boolean',
            'n_popup_show:boolean',
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'n_read_dt'
            ],
            /*[
                'attribute' => 'n_read_dt',
                'value' => static function (\common\models\Notifications $model) {
                    return $model->n_read_dt ? '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->n_read_dt)) : '-';
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'n_read_dt',
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
                'class' => DateTimeColumn::class,
                'attribute' => 'n_created_dt'
            ],

            /*[
                'attribute' => 'n_created_dt',
                'value' => static function (\common\models\Notifications $model) {
                    return '<i class="fa fa-calendar"></i> ' . Yii::$app->formatter->asDatetime(strtotime($model->n_created_dt));
                },
                'format' => 'raw',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'n_created_dt',
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

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
