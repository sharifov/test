<?php

/**
 * @var View $this
 * @var ShiftScheduleRequest $model
 */

use modules\shiftSchedule\src\entities\shiftScheduleRequest\ShiftScheduleRequest;
use yii\helpers\Html;
use yii\web\View;

$this->title = 'Update Shift Schedule Request: ' . $model->ssr_id;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ssr_id, 'url' => ['view', 'ssr_id' => $model->ssr_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shift-schedule-request-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
