<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegmentStop */

$this->title = $model->qss_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Segment Stops', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="flight-quote-segment-stop-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->qss_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->qss_id], [
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
            'qss_id',
            'qss_quote_segment_id',
            'qss_location_iata',
            'qss_equipment',
            'qss_elapsed_time:datetime',
            'qss_duration',
            'qss_departure_dt',
            'qss_arrival_dt',
        ],
    ]) ?>

</div>
