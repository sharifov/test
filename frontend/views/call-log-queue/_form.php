<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\callLog\entity\callLogQueue\CallLogQueue */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-log-queue-form">

    <div class="row">
        <div class="col-md-4">


            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'clq_cl_id')->textInput() ?>

            <?= $form->field($model, 'clq_queue_time')->textInput() ?>

            <?= $form->field($model, 'clq_access_count')->textInput() ?>

            <?= $form->field($model, 'clq_is_transfer')->dropDownList([0 => 'No', 1 => 'Yes'], ['prompt' => 'Select...']) ?>

            <div class="form-group">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
