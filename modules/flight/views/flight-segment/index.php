<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightSegmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Segments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-segment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Segment', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'fs_id',
            'fs_flight_id',
            'fs_origin_iata',
            'fs_destination_iata',
            //'fs_departure_date',
            [
                'attribute' => 'fs_departure_date',
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'fs_departure_date',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd',
                        'clearBtn' => true,
                        'endDate' => date('Y-m-d', time())
                    ],
                    'options' => [
                        'autocomplete' => 'off',
                        'placeholder' =>'Choose Date'
                    ],
                    'containerOptions' => [
                        'class' => (array_key_exists('fs_departure_date', $searchModel->errors)) ? 'has-error' : null,
                    ],
                    'clientEvents' => [
                        'clearDate' => 'function (e) {$(e.target).find("input").change();}',
                    ],
                ]),
            ],
            'fs_flex_type_id',
            'fs_flex_days',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
