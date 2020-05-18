<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\email\src\entity\emailAccount\search\EmailAccountSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="email-account-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ea_id') ?>

    <?= $form->field($model, 'ea_email') ?>

    <?= $form->field($model, 'ea_imap_settings') ?>

    <?= $form->field($model, 'ea_gmail_settings') ?>

    <?= $form->field($model, 'ea_gmail_token') ?>

    <?php // echo $form->field($model, 'ea_protocol') ?>

    <?php // echo $form->field($model, 'ea_options') ?>

    <?php // echo $form->field($model, 'ea_active') ?>

    <?php // echo $form->field($model, 'ea_created_user_id') ?>

    <?php // echo $form->field($model, 'ea_updated_user_id') ?>

    <?php // echo $form->field($model, 'ea_created_dt') ?>

    <?php // echo $form->field($model, 'ea_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
