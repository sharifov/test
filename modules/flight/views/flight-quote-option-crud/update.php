<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteOption\FlightQuoteOption */

$this->title = 'Update Flight Quote Option: ' . $model->fqo_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fqo_id, 'url' => ['view', 'id' => $model->fqo_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-quote-option-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
