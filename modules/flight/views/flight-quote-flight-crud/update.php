<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteFlight */

$this->title = 'Update Flight Quote Flight: ' . $model->fqf_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Flights', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fqf_id, 'url' => ['view', 'id' => $model->fqf_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-flight-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
