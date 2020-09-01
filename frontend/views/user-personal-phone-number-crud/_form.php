<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\userPersonalPhoneNumber\entity\UserPersonalPhoneNumber */
/* @var $form ActiveForm */
?>

<div class="user-personal-phone-number-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'upn_user_id')->widget(\sales\widgets\UserSelect2Widget::class) ?>

        <?= $form->field($model, 'upn_phone_number')->widget(\sales\widgets\PhoneSelect2Widget::class) ?>

        <?= $form->field($model, 'upn_title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'upn_approved')->checkbox() ?>

        <?= $form->field($model, 'upn_enabled')->checkbox() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
