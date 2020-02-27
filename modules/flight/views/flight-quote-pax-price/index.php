<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightQuotePaxPriceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quote Pax Prices';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-pax-price-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Quote Pax Price', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'qpp_id',
            'qpp_flight_quote_id',
            'qpp_flight_pax_code_id',
            'qpp_fare',
            'qpp_tax',
            'qpp_system_mark_up',
            'qpp_agent_mark_up',
            'qpp_origin_fare',
            'qpp_origin_currency',
            'qpp_origin_tax',
            'qpp_client_currency',
            'qpp_client_fare',
            'qpp_client_tax',
            'qpp_cnt',
            'qpp_created_dt',
            'qpp_updated_dt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
