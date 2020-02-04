<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegmentPaxBaggageCharge */

$this->title = 'Update Flight Quote Segment Pax Baggage Charge: ' . $model->qsbc_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Segment Pax Baggage Charges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qsbc_id, 'url' => ['view', 'id' => $model->qsbc_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-segment-pax-baggage-charge-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
