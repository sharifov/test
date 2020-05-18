<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ClientPhone */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-phone-form">
    <?php $form = ActiveForm::begin(); ?>

    <?php //= $form->field($model, 'client_id')->textInput() ?>

    <div class="col-md-6">
        <?= $form->field($model, 'client_id')->input('number', ['min' => 1]) ?>

        <?= $form->field($model, 'phone', ['enableClientValidation' => false])->textInput(['maxlength' => true]) ?>

        <?php //= $form->field($model, 'created')->textInput() ?>
        <?php //= $form->field($model, 'updated')->textInput() ?>

        <?= $form->field($model, 'type')->dropDownList($model::getPhoneTypeList()) ?>

        <?= $form->field($model, 'comments')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'is_sms')->textInput() ?>

        <?= $form->field($model, 'validate_dt')->textInput() ?>

        <?= $form->field($model, 'cp_title')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
