<?php

use dosamigos\datepicker\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use common\components\grid\DateTimeColumn;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\QuotePriceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Quote Prices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="quote-price-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //= Html::a('Create Quote Price', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],


            [
                'attribute' => 'id',
                'options' => ['style' => 'width:80px'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'quote_id',
                'options' => ['style' => 'width:120px'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'quote.uid',
                'header'    => 'Quote UID',
                'options' => ['style' => 'width:120px'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            [
                'attribute' => 'quote.status',
                'header'    => 'Quote Status',
                'value' => function (\common\models\QuotePrice $model) {
                    return $model->quote ? $model->quote->getStatusName(true) : '-';
                },
                'format' => 'html',
                'options' => ['style' => 'width:120px'],
                'contentOptions' => ['class' => 'text-center'],
            ],
            //'passenger_type',
            [
                'attribute' => 'passenger_type',
                'value' => function (\common\models\QuotePrice $model) {
                    return '<i class="fa fa-user"></i> ' . $model->getPassengerTypeName();
                },
                'format' => 'raw',
                'filter' => \common\models\QuotePrice::PASSENGER_TYPE_LIST
            ],

            [
                'attribute' => 'selling',
                'options' => ['style' => 'width:110px'],
                //'format' => ['decimal',2],
                'contentOptions' => ['class' => 'text-right'],
                'format' => 'currency'
            ],
            [
                'attribute' => 'net',
                'options' => ['style' => 'width:110px'],
                'contentOptions' => ['class' => 'text-right'],
                'format' => 'currency'
            ],
            [
                'attribute' => 'fare',
                'options' => ['style' => 'width:110px'],
                'contentOptions' => ['class' => 'text-right'],
                'format' => 'currency'
            ],

            [
                'attribute' => 'taxes',
                'options' => ['style' => 'width:110px'],
                'contentOptions' => ['class' => 'text-right'],
                'format' => 'currency'
            ],

            [
                'attribute' => 'mark_up',
                'options' => ['style' => 'width:110px'],
                'contentOptions' => ['class' => 'text-right'],
                'format' => 'currency'
            ],
            [
                'attribute' => 'extra_mark_up',
                'options' => ['style' => 'width:110px'],
                'contentOptions' => ['class' => 'text-right'],
                'format' => 'currency'
            ],

            /*[
                'attribute' => 'created',
                'value' => function(\common\models\QuotePrice $model) {
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
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],*/

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'created'
            ],

            /*[
                'attribute' => 'updated',
                'value' => function(\common\models\QuotePrice $model) {
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
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                ]),
            ],*/

            [
                'class' => DateTimeColumn::class,
                'attribute' => 'updated'
            ],

            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>

</div>
