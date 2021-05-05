<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuoteFlight */

$this->title = 'Create Flight Quote Flight';
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Flights', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-flight-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
