<?php

use modules\shiftSchedule\src\forms\ShiftScheduleTypeForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ShiftScheduleTypeForm */

$this->title = 'Update Shift Schedule Type: ' . $model->sst_id;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->sst_id, 'url' => ['view', 'sst_id' => $model->sst_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shift-schedule-type-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
