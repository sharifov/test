<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\phoneLine\userPersonalPhoneNumber\entity\search\UserPersonalPhoneNumberSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="user-personal-phone-number-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'upn_id') ?>

    <?= $form->field($model, 'upn_user_id') ?>

    <?= $form->field($model, 'upn_phone_number') ?>

    <?= $form->field($model, 'upn_title') ?>

    <?= $form->field($model, 'upn_approved') ?>

    <?php // echo $form->field($model, 'upn_enabled') ?>

    <?php // echo $form->field($model, 'upn_created_user_id') ?>

    <?php // echo $form->field($model, 'upn_updated_user_id') ?>

    <?php // echo $form->field($model, 'upn_created_dt') ?>

    <?php // echo $form->field($model, 'upn_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
