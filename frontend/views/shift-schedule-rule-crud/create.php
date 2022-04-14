<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule */

$this->title = 'Create Shift Schedule Rule';
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-schedule-rule-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
