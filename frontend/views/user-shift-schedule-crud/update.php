<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model \modules\shiftSchedule\src\entities\userShiftSchedule\UserShiftSchedule */

$this->title = 'Update User Shift Schedule: ' . $model->uss_id;
$this->params['breadcrumbs'][] = ['label' => 'User Shift Schedules', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->uss_id, 'url' => ['view', 'id' => $model->uss_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-shift-schedule-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
