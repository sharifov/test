<?php

use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteOption\FlightQuoteOption */

$this->title = $model->fqo_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-quote-option-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="col-md-4">

        <p>
            <?= Html::a('Update', ['update', 'id' => $model->fqo_id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->fqo_id], [
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
                'fqo_id',
                'fqo_product_quote_option_id',
                'fqo_flight_pax_id',
                'fqo_flight_quote_segment_id',
                'fqo_flight_quote_trip_id',
                'fqo_display_name',
                'fqo_markup_amount',
                'fqo_base_price',
                'fqo_total_price',
                'fqo_client_total',
            ],
        ]) ?>

    </div>

</div>
