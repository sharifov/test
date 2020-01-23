<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteStatusLog */

$this->title = 'Update Flight Quote Status Log: ' . $model->qsl_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Status Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->qsl_id, 'url' => ['view', 'id' => $model->qsl_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-status-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
