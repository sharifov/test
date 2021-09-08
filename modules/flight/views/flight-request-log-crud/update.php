<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightRequestLog */

$this->title = 'Update Flight Request Log: ' . $model->flr_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Request Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->flr_id, 'url' => ['view', 'id' => $model->flr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-request-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
