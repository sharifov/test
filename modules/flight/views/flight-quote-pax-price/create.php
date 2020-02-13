<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightQuotePaxPrice */

$this->title = 'Create Flight Quote Pax Price';
$this->params['breadcrumbs'][] = ['label' => 'Flight Quote Pax Prices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-quote-pax-price-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
