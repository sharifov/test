<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\shiftScheduleRule\ShiftScheduleRule */

$this->title = 'Update Shift Schedule Rule: ' . $model->ssr_id;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Rules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ssr_id, 'url' => ['view', 'id' => $model->ssr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shift-schedule-rule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
