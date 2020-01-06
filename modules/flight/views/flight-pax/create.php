<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightPax */

$this->title = 'Create Flight Pax';
$this->params['breadcrumbs'][] = ['label' => 'Flight Paxes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-pax-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
