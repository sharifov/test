<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteOption\FlightQuoteOption */

$this->title = 'Create Flight Quote Option';
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Options', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-option-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
