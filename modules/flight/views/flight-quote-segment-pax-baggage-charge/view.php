<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegmentPaxBaggageCharge */

$this->title = $model->qsbc_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Segment Pax Baggage Charges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-quote-segment-pax-baggage-charge-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->qsbc_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->qsbc_id], [
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
            'qsbc_id',
            'qsbc_flight_pax_code_id',
            'qsbc_flight_quote_segment_id',
            'qsbc_first_piece',
            'qsbc_last_piece',
            'qsbc_origin_price',
            'qsbc_origin_currency',
            'qsbc_price',
            'qsbc_client_price',
            'qsbc_client_currency',
            'qsbc_max_weight',
            'qsbc_max_size',
        ],
    ]) ?>

</div>
