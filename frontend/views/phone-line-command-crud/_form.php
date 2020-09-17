<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\call\entity\callCommand\PhoneLineCommand */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="phone-line-command-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'plc_line_id')->textInput() ?>

    <?= $form->field($model, 'plc_ccom_id')->textInput() ?>

    <?= $form->field($model, 'plc_sort_order')->textInput() ?>

    <?= $form->field($model, 'plc_created_user_id')->textInput() ?>

    <?= $form->field($model, 'plc_created_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
