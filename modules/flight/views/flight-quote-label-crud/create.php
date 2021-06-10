<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\src\entities\flightQuoteLabel\FlightQuoteLabel */

$this->title = 'Create Flight Quote Label';
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Labels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-label-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
