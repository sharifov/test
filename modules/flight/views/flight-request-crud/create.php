<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightRequest */

$this->title = 'Create Flight Request';
$this->params['breadcrumbs'][] = ['label' => 'Flight Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
