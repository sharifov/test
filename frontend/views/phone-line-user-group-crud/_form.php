<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\phoneLineUserGroup\entity\PhoneLineUserGroup */
/* @var $form ActiveForm */
?>

<div class="phone-line-user-group-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'plug_line_id')->textInput() ?>

        <?= $form->field($model, 'plug_ug_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
