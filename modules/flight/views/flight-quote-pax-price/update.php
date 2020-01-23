<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuotePaxPrice */

$this->title = 'Update Flight Quote Pax Price: ' . $model->qpp_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Pax Prices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qpp_id, 'url' => ['view', 'id' => $model->qpp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-pax-price-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
