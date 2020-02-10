<?php

use modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\qaTask\src\entities\qaTaskStatus\QaTaskStatus */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qa-task-status-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ts_id')->dropDownList(QaTaskStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'ts_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ts_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ts_enabled')->dropDownList([1 => 'Yes', 0 => 'No']) ?>

        <?= $form->field($model, 'ts_css_class')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
