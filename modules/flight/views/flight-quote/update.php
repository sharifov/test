<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuote */

$this->title = 'Update Flight Quote: ' . $model->fq_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quotes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fq_id, 'url' => ['view', 'id' => $model->fq_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
