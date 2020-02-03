<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteTrip */

$this->title = 'Create Flight Quote Trip';
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Trips', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-trip-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
