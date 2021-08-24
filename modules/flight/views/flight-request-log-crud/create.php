<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightRequestLog */

$this->title = 'Create Flight Request Log';
$this->params['breadcrumbs'][] = ['label' => 'Flight Request Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flight-request-log-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
