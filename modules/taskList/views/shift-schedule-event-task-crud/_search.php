<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTaskSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="shift-schedule-event-task-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'sset_event_id') ?>

    <?= $form->field($model, 'sset_user_task_id') ?>

    <?= $form->field($model, 'sset_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
