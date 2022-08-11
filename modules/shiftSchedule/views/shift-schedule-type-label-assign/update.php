<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign */

$this->title = 'Update Shift Schedule Type Label Assign: ' . $model->tla_stl_key;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Type Label Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->tla_stl_key, 'url' => ['view', 'tla_stl_key' => $model->tla_stl_key, 'tla_sst_id' => $model->tla_sst_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shift-schedule-type-label-assign-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
