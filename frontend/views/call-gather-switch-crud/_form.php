<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\CallGatherSwitch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-gather-switch-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cgs_ccom_id')->textInput() ?>

    <?= $form->field($model, 'cgs_step')->textInput() ?>

    <?= $form->field($model, 'cgs_case')->textInput() ?>

    <?= $form->field($model, 'cgs_exec_ccom_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
