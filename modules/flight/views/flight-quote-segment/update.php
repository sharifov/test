<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegment */

$this->title = 'Update Flight Quote Segment: ' . $model->fqs_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Segments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fqs_id, 'url' => ['view', 'id' => $model->fqs_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-segment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
