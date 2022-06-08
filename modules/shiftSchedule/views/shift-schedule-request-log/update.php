<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model modules\shiftSchedule\src\entities\shiftScheduleRequestLog\ShiftScheduleRequestLog */

$this->title = 'Update Shift Schedule Request History: ' . $model->ssrh_id;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Request Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ssrh_id, 'url' => ['view', 'ssrh_id' => $model->ssrh_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shift-schedule-request-history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
