<?php

use modules\taskList\src\entities\userTask\UserTask;
use src\widgets\DateTimePicker;
use src\widgets\UserSelect2Widget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\taskList\src\entities\userTask\UserTaskStatusLog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-task-status-log-form col-md-4">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'utsl_ut_id')->textInput() ?>

    <?= $form->field($model, 'utsl_description')->textarea(['maxlength' => true]) ?>

    <?= $form->field($model, 'utsl_old_status')->dropDownList(UserTask::STATUS_LIST, ['prompt' => 'Select status']) ?>

    <?= $form->field($model, 'utsl_new_status')->dropDownList(UserTask::STATUS_LIST, ['prompt' => 'Select status']) ?>

    <?= $form->field($model, 'utsl_created_user_id')->widget(UserSelect2Widget::class, [
        'data' => $model->utsl_created_user_id ? [
            $model->utsl_created_user_id => $model->utslCreatedUser->username
        ] : [],
    ]) ?>

    <?= $form->field($model, 'utsl_created_dt')->widget(DateTimePicker::class) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
