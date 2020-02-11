<?php

use modules\qaTask\src\entities\QaObjectType;
use modules\qaTask\src\entities\qaTaskActionReason\QaTaskActionReason;
use modules\qaTask\src\useCases\qaTask\QaTaskActions;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model QaTaskActionReason */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qa-task-action-reason-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'tar_object_type_id')->dropDownList(QaObjectType::getList(), ['prompt' => 'Select Object Type']) ?>

        <?= $form->field($model, 'tar_action_id')->dropDownList(QaTaskActions::getList(), ['prompt' => 'Select action']) ?>

        <?= $form->field($model, 'tar_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tar_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tar_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'tar_comment_required')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <?= $form->field($model, 'tar_enabled')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
