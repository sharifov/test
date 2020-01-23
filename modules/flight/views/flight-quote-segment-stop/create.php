<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegmentStop */

$this->title = 'Create Flight Quote Segment Stop';
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Segment Stops', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-segment-stop-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
