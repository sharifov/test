<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\LeadTask */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-task-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
    <?= $form->field($model, 'lt_lead_id')->textInput() ?>

    <?= $form->field($model, 'lt_task_id')->dropDownList(\common\models\Task::getList()) ?>

    <?= $form->field($model, 'lt_user_id')->dropDownList(\common\models\Employee::getList()) ?>

    <?= $form->field($model, 'lt_date')->input('date') ?>

    <?= $form->field($model, 'lt_notes')->textarea(['rows' => 5, 'maxlength' => true]) ?>

    <?= $form->field($model, 'lt_completed_dt')->input('datetime') ?>

    <?php //= $form->field($model, 'lt_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
