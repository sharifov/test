<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogCase\CallLogCase */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-log-case-form">

    <div class="row">
        <div class="col-md-4">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'clc_cl_id')->textInput() ?>

            <?= $form->field($model, 'clc_case_id')->textInput() ?>

            <?= $form->field($model, 'clc_case_status_log_id')->textInput() ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
