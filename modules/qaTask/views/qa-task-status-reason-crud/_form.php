<?php

use modules\qaTask\src\entities\QaObjectType;
use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskStatusReason\QaTaskStatusReason */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qa-task-status-reason-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'tsr_object_type_id')->dropDownList(QaObjectType::getList(), ['prompt' => 'Select Object Type']) ?>

        <?= $form->field($model, 'tsr_status_id')->dropDownList(QaTaskStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'tsr_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tsr_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tsr_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tsr_comment_required')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <?= $form->field($model, 'tsr_enabled')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
