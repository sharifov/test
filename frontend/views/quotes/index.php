<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\QuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quotes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?//= Html::a('Create Quote', ['create'], ['class' => 'btn btn-success']) ?>
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

            'id',
            'uid',
            [
                'attribute' => 'lead_id',
                'format' => 'raw',
                'value' => function(\common\models\Quote $model) {
                    return '<i class="fa fa-arrow-right"></i> '.Html::a('lead: '.$model->lead_id, ['lead/view', 'gid' => $model->lead->gid], ['target' => '_blank', 'data-pjax' => 0]);
                },
            ],
            [
                'attribute' => 'employee_id',
                'format' => 'raw',
                'value' => function(\common\models\Quote $model) {
                    return $model->employee ? '<i class="fa fa-user"></i> '.$model->employee->username : '-';
                },
                'filter' => \common\models\Employee::getList()
            ],
            'record_locator',


            [
                'attribute' => 'gds',
                'value' => function(\common\models\Quote $model) {
                    return '<i class="fa fa-plane"></i> '.$model->getGdsName2();
                },
                'format' => 'raw',
                'filter' => \common\models\Quote::GDS_LIST
            ],
            'pcc',
            [
                'attribute' => 'trip_type',
                'value' => function(\common\models\Quote $model) {
                    return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                },
                'filter' => \common\models\Lead::TRIP_TYPE_LIST
            ],
            [
                'attribute' => 'cabin',
                'value' => function(\common\models\Quote $model) {
                    return \common\models\Lead::getCabin($model->cabin) ?? '-';
                },
                'filter' => \common\models\Lead::CABIN_LIST
            ],
            'main_airline_code',
            //'reservation_dump:ntext',
            [
                'attribute' => 'reservation_dump',
                'value' => function(\common\models\Quote $model) {
                    return '<pre style="font-size: 9px">'.$model->reservation_dump.'</pre>';
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'status',
                'value' => function(\common\models\Quote $model) {
                    return $model->getStatusName(true);
                },
                'format' => 'html',
                'filter' => \common\models\Quote::STATUS_LIST
            ],
            'check_payment:boolean',
            'fare_type',
            [
                'header' => 'Prices',
                'value' => function(\common\models\Quote $model) {
                    return $model->quotePricesCount ? Html::a($model->quotePricesCount, ['quote-price/index', "QuotePriceSearch[quote_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ],

            //'created',
            //'updated',

            [
                'attribute' => 'created',
                'value' => function(\common\models\Quote $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->created));
                },
                'format' => 'html',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'created',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ],
                ]),
            ],

            [
                'attribute' => 'updated',
                'value' => function(\common\models\Quote $model) {
                    return '<i class="fa fa-calendar"></i> '.Yii::$app->formatter->asDatetime(strtotime($model->updated));
                },
                'format' => 'html',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updated',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ],
                ]),
            ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
