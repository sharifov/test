<?php

use common\components\SearchService;
use dosamigos\datepicker\DatePicker;
use modules\lead\src\grid\columns\LeadColumn;
use common\components\grid\DateTimeColumn;
use common\components\grid\quote\QuoteTypeColumn;
use common\components\grid\UserColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
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
    <?php Pjax::begin(['timeout' => 5000, 'scrollTo' => 0]); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?php //= Html::a('Create Quote', ['create'], ['class' => 'btn btn-success']) ?>
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
                'model' => $searchModel,
                'attribute' => 'date_range',
                'useWithAddon' => true,
                'presetDropdown' => true,
                'hideInput' => true,
                'convertFormat' => true,
                'startAttribute' => 'datetime_start',
                'endAttribute' => 'datetime_end',
                'pluginOptions' => [
                    'timePicker' => true,
                    'timePickerIncrement' => 1,
                    'timePicker24Hour' => true,
                    'locale' => [
                        'format' => 'Y-m-d H:i',
                        'separator' => ' - '
                    ],
                    'ranges' => \Yii::$app->params['dateRangePicker']['configs']['default']
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

    <?php  echo $this->render('_pagination', ['model' => $searchModel]);?>
    <?= $searchModel->filterCount ? 'Find <b>' . $searchModel->filterCount . '</b> items' : null ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'filterUrl' => Url::to(['quotes/index']),
        'layout' => "{items}",
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'id',
                'enableSorting' => false,
            ],
            'uid',
            [
                'class' => LeadColumn::class,
                'attribute' => 'lead_id',
                'relation' => 'lead',
            ],

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'employee_id',
                'relation' => 'employee',
                'placeholder' => 'Select User',
            ],

            'record_locator',

            [
                'attribute' => 'gds',
                'value' => function (\common\models\Quote $model) {
                    return '<i class="fa fa-plane"></i> ' . $model->getGdsName2();
                },
                'format' => 'raw',
                'filter' => SearchService::GDS_LIST
            ],
            'pcc',
            [
                'attribute' => 'trip_type',
                'value' => function (\common\models\Quote $model) {
                    return \common\models\Lead::getFlightType($model->trip_type) ?? '-';
                },
                'filter' => \common\models\Lead::TRIP_TYPE_LIST
            ],
            [
                'attribute' => 'cabin',
                'value' => function (\common\models\Quote $model) {
                    return \common\models\Lead::getCabin($model->cabin) ?? '-';
                },
                'filter' => \common\models\Lead::CABIN_LIST
            ],
            'main_airline_code',
            //'reservation_dump:ntext',
            [
                'attribute' => 'reservation_dump',
                'value' => function (\common\models\Quote $model) {
                    return '<pre style="font-size: 9px">' . $model->reservation_dump . '</pre>';
                },
                'format' => 'html',
            ],
            [
                'attribute' => 'agent_processing_fee'
            ],
            [
                'attribute' => 'status',
                'value' => function (\common\models\Quote $model) {
                    return $model->getStatusName(true);
                },
                'format' => 'html',
                'filter' => \common\models\Quote::STATUS_LIST
            ],
            'check_payment:boolean',
            ['class' => QuoteTypeColumn::class],
            'fare_type',
            [
                'header' => 'Prices',
                'value' => function (\common\models\Quote $model) {
                    return $model->quotePricesCount ? Html::a($model->quotePricesCount, ['quote-price/index', "QuotePriceSearch[quote_id]" => $model->id], ['target' => '_blank', 'data-pjax' => 0]) : '-' ;
                },
                'format' => 'raw',
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'q_create_type_id',
                'value' => function (\common\models\Quote $model) {
                    return $model->getCreateTypeName();
                },
                'filter' => \common\models\Quote::CREATE_TYPE_LIST
            ],
            [
                'class' => DateTimeColumn::class,
                'attribute' => 'created',
            ],

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'updated',
            ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
