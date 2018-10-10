<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserGroupAssign */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-group-assign-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-3">
        <?= $form->field($model, 'ugs_user_id')->dropDownList(\common\models\Employee::getList()) ?>

        <?= $form->field($model, 'ugs_group_id')->dropDownList(\common\models\UserGroup::getList()) ?>

        <?//= $form->field($model, 'ugs_updated_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
