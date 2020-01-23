<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegmentPaxBaggageCharge */

$this->title = 'Create Flight Quote Segment Pax Baggage Charge';
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Segment Pax Baggage Charges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-segment-pax-baggage-charge-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
