<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegmentPaxBaggage */

$this->title = 'Update Flight Quote Segment Pax Baggage: ' . $model->qsb_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Segment Pax Baggages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qsb_id, 'url' => ['view', 'id' => $model->qsb_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-segment-pax-baggage-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
