<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\flight\models\FlightSegment */

$this->title = 'Update Flight Segment: ' . $model->fs_id;
$this->params['breadcrumbs'][] = ['label' => 'Flight Segments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->fs_id, 'url' => ['view', 'id' => $model->fs_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="flight-segment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
