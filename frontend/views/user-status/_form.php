<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\userStatus\UserStatus */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-status-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6">
    <?= $form->field($model, 'us_user_id')->input('number', ['min' => 0]) ?>

    <?= $form->field($model, 'us_gl_call_count')->input('number', ['min' => 0]) ?>

    <?= $form->field($model, 'us_call_phone_status')->checkbox() ?>

    <?= $form->field($model, 'us_is_on_call')->checkbox() ?>

    <?= $form->field($model, 'us_has_call_access')->checkbox() ?>


    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
