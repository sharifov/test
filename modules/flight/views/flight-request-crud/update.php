<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightRequest */

$this->title = 'Update Flight Request: ' . $model->fr_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fr_id, 'url' => ['view', 'fr_id' => $model->fr_id, 'fr_year' => $model->fr_year, 'fr_month' => $model->fr_month]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-request-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
