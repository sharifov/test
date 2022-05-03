<?php

use modules\shiftSchedule\src\forms\ShiftScheduleTypeForm;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model ShiftScheduleTypeForm */

$this->title = 'Create Shift Schedule Type';
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-schedule-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
