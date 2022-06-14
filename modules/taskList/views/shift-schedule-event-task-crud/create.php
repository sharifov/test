<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask */

$this->title = 'Create Shift Schedule Event Task';
$this->params['breadcrumbs'][] = ['label' => 'Shift Schedule Event Tasks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shift-schedule-event-task-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
