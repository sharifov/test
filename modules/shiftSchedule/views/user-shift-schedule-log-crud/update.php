<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\userShiftScheduleLog\UserShiftScheduleLog */

$this->title = 'Update User Shift Schedule Log: ' . $model->ussl_id;
$this->params['breadcrumbs'][] = ['label' => 'User Shift Schedule Logs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ussl_id, 'url' => ['view', 'ussl_id' => $model->ussl_id, 'ussl_month_start' => $model->ussl_month_start, 'ussl_year_start' => $model->ussl_year_start]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-shift-schedule-log-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
