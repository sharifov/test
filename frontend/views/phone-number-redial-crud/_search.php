<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\phoneNumberRedial\entity\PhoneNumberRedialSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="phone-number-redial-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pnr_id') ?>

    <?= $form->field($model, 'pnr_project_id') ?>

    <?= $form->field($model, 'pnr_phone_pattern') ?>

    <?= $form->field($model, 'pnr_pl_id') ?>

    <?= $form->field($model, 'pnr_name') ?>

    <?php // echo $form->field($model, 'pnr_enabled') ?>

    <?php // echo $form->field($model, 'pnr_priority') ?>

    <?php // echo $form->field($model, 'pnr_created_dt') ?>

    <?php // echo $form->field($model, 'pnr_updated_dt') ?>

    <?php // echo $form->field($model, 'pnr_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
