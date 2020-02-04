<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegmentPaxBaggage */

$this->title = 'Create Flight Quote Segment Pax Baggage';
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Segment Pax Baggages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-segment-pax-baggage-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
