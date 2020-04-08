<?php

use common\components\grid\BooleanColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flights';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'fl_id',
            'fl_product_id',
            [
                'attribute' => 'fl_trip_type_id',
                'value' => static function (\modules\flight\models\Flight $model) {
                    return $model->tripTypeName;
                },
                'filter' => \modules\flight\models\Flight::getTripTypeList()
            ],
            [
                'attribute' => 'fl_cabin_class',
                'value' => static function (\modules\flight\models\Flight $model) {
                    return $model->cabinClassName;
                },
                'filter' => \modules\flight\models\Flight::getCabinClassList()
            ],
            //'fl_trip_type_id',
            //'fl_cabin_class',
            'fl_adults',
            'fl_children',
            'fl_infants',
            'fl_stops',
            [
                'class' => BooleanColumn::class,
                'attribute' => 'fl_delayed_charge',
            ],
            'fl_request_hash_key',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
