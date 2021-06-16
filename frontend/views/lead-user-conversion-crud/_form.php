<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\leadUserConversion\entity\LeadUserConversion */
/* @var $form ActiveForm */
?>

<div class="lead-user-conversion-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'luc_lead_id')->textInput() ?>

        <?= $form->field($model, 'luc_user_id')->textInput() ?>

        <?= $form->field($model, 'luc_description')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
