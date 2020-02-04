<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightPax */

$this->title = 'Update Flight Pax: ' . $model->fp_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Paxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fp_id, 'url' => ['view', 'id' => $model->fp_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-pax-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
