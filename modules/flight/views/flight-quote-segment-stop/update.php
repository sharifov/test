<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegmentStop */

$this->title = 'Update Flight Quote Segment Stop: ' . $model->qss_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Segment Stops', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qss_id, 'url' => ['view', 'id' => $model->qss_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-segment-stop-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
