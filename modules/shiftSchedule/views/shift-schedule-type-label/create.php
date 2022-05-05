<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleTypeLabel\ShiftScheduleTypeLabel */

$this->title = 'Create Shift Schedule Type Label';
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Type Labels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-schedule-type-label-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
