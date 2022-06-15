<?php

use modules\experiment\models\ExperimentTarget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\experiment\models\ExperimentTarget */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="experiment-target-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ext_target_id')->textInput() ?>

    <?= $form->field($model, 'ext_target_type')->dropDownList(ExperimentTarget::getList(), ['prompt' => '---'])?>

    <?= $form->field($model, 'ext_experiment_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
