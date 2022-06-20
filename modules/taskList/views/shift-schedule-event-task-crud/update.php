<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask */

$this->title = 'Update Shift Schedule Event Task: ' . $model->sset_event_id;
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Event Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->sset_event_id, 'url' => ['view', 'sset_event_id' => $model->sset_event_id, 'sset_user_task_id' => $model->sset_user_task_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="shift-schedule-event-task-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
