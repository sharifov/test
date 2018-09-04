<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LeadFlightSegment */

$this->title = 'Update Lead Flight Segment: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Lead Flight Segments', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="lead-flight-segment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
