<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\leadBusinessExtraQueueRule\entity\LeadBusinessExtraQueueRule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="lead-business-extra-queue-rule-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'lbeqr_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lbeqr_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'lbeqr_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'lbeqr_params_json')->textInput() ?>

    <?= $form->field($model, 'lbeqr_updated_user_id')->textInput() ?>

    <?= $form->field($model, 'lbeqr_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
