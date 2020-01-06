<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegmentPaxBaggage */

$this->title = $model->qsb_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Segment Pax Baggages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-quote-segment-pax-baggage-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->qsb_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->qsb_id], [
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
            'qsb_id',
            'qsb_flight_pax_code_id',
            'qsb_flight_quote_segment_id',
            'qsb_airline_code',
            'qsb_allow_pieces',
            'qsb_allow_weight',
            'qsb_allow_unit',
            'qsb_allow_max_weight',
            'qsb_allow_max_size',
        ],
    ]) ?>

</div>
