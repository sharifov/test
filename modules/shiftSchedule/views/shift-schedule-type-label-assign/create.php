<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleTypeLabelAssign\ShiftScheduleTypeLabelAssign */

$this->title = 'Create Shift Schedule Type Label Assign';
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Type Label Assigns', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-schedule-type-label-assign-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
