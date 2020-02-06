<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteTrip */

$this->title = 'Update Flight Quote Trip: ' . $model->fqt_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Trips', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fqt_id, 'url' => ['view', 'id' => $model->fqt_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-trip-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
