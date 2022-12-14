<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel */

$this->title = 'Update Shift Schedule Type Label: ' . $model->stl_key;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Type Labels', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->stl_key, 'url' => ['view', 'stl_key' => $model->stl_key]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shift-schedule-type-label-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
