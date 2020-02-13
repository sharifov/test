<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteSegment */

$this->title = 'Create Flight Quote Segment';
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Segments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-segment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
