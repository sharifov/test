<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\shiftScheduleEventTask\ShiftScheduleEventTask */
/* @var $form ActiveForm */
?>

<div class="shift-schedule-event-task-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'sset_event_id')->textInput() ?>

        <?= $form->field($model, 'sset_user_task_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
