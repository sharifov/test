<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel modules\flight\models\search\FlightQuoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Flight Quotes';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Flight Quote', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'fq_id',
            'fq_flight_id',
            'fq_source_id',
            'fq_product_quote_id',
            'fq_hash_key',
            'fq_uid',
            'fq_service_fee_percent',
            'fq_record_locator',
            'fq_gds',
            'fq_gds_pcc',
            'fq_gds_offer_id',
            'fq_type_id',
            'fq_cabin_class',
            'fq_trip_type_id',
            'fq_main_airline',
            'fq_fare_type_id',
            //'fq_created_user_id',

            [
                'class' => \common\components\grid\UserSelect2Column::class,
                'attribute' => 'fq_created_user_id',
                'relation' => 'fqCreatedUser',
                'placeholder' => 'Select User',
            ],

            'fq_created_expert_id',
            'fq_created_expert_name',
            'fq_reservation_dump:ntext',
            'fq_pricing_info:ntext',
            'fq_origin_search_data',
            'fq_last_ticket_date',
            'fq_request_hash',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
