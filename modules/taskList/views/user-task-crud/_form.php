<?php

use modules\taskList\src\entities\TargetObject;
use modules\taskList\src\entities\userTask\UserTask;
use src\widgets\UserSelect2Widget;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;
use src\widgets\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\userTask\UserTask */
/* @var $form ActiveForm */
?>

<div class="user-task-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ut_user_id')->widget(UserSelect2Widget::class, [
            'data' => $model->ut_user_id ? [
                $model->ut_user_id => $model->user->username
            ] : [],
        ]) ?>

        <?= $form->field($model, 'ut_target_object')->dropDownList(TargetObject::TARGET_OBJ_LIST, ['prompt' => 'Select target object']) ?>

        <?= $form->field($model, 'ut_target_object_id')->textInput() ?>

        <?= $form->field($model, 'ut_task_list_id')->textInput() ?>

        <?= $form->field($model, 'ut_start_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'ut_end_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'ut_priority')->dropDownList(UserTask::PRIORITY_LIST, ['prompt' => 'Select priority']) ?>

        <?= $form->field($model, 'ut_status_id')->dropDownList(UserTask::STATUS_LIST, ['prompt' => 'Select status']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
