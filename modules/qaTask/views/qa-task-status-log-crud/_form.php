<?php

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatusAction;
use modules\qaTask\src\entities\qaTaskStatusLog\QaTaskStatusLog;
use modules\qaTask\src\entities\qaTaskStatusReason\QaTaskStatusReasonQuery;
use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model QaTaskStatusLog */
/* @var $form yii\widgets\ActiveForm */

$list = new ListsAccess(Auth::id());

?>

<div class="qa-task-status-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'tsl_task_id')->textInput() ?>

        <?= $form->field($model, 'tsl_start_status_id')->dropDownList(QaTaskStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'tsl_end_status_id')->dropDownList(QaTaskStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'tsl_start_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'tsl_end_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'tsl_duration')->textInput() ?>

        <?= $form->field($model, 'tsl_reason_id')->dropDownList(QaTaskStatusReasonQuery::getList(), ['prompt' => 'Select reason']) ?>

        <?= $form->field($model, 'tsl_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tsl_action_id')->dropDownList(QaTaskStatusAction::getList(), ['prompt' => 'Select action']) ?>

        <?= $form->field($model, 'tsl_assigned_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <?= $form->field($model, 'tsl_created_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
