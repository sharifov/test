<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CallUserAccess */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="call-user-access-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
    <?= $form->field($model, 'cua_call_id')->input('number', ['min' => 1]) ?>

    <?= $form->field($model, 'cua_user_id')->dropDownList(\common\models\Employee::getList()) ?>

    <?= $form->field($model, 'cua_status_id')->dropDownList(\common\models\CallUserAccess::getStatusTypeList()) ?>

    <?php //= $form->field($model, 'cua_created_dt')->textInput() ?>

    <?php //= $form->field($model, 'cua_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
