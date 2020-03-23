<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuote */

$this->title = $model->fq_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-quote-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->fq_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->fq_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
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
            'fq_created_user_id',
            'fq_created_expert_id',
            'fq_created_expert_name',
            'fq_reservation_dump:ntext',
            'fq_pricing_info:ntext',
            'fq_origin_search_data',
            'fq_last_ticket_date',
            'fq_request_hash'
        ],
    ]) ?>

</div>
