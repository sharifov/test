<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\sms\entity\smsDistributionList\search\SmsDistributionListSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sms-distribution-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'sdl_id') ?>

    <?= $form->field($model, 'sdl_com_id') ?>

    <?= $form->field($model, 'sdl_project_id') ?>

    <?= $form->field($model, 'sdl_phone_from') ?>

    <?= $form->field($model, 'sdl_phone_to') ?>

    <?php // echo $form->field($model, 'sdl_client_id') ?>

    <?php // echo $form->field($model, 'sdl_text') ?>

    <?php // echo $form->field($model, 'sdl_start_dt') ?>

    <?php // echo $form->field($model, 'sdl_end_dt') ?>

    <?php // echo $form->field($model, 'sdl_status_id') ?>

    <?php // echo $form->field($model, 'sdl_priority') ?>

    <?php // echo $form->field($model, 'sdl_error_message') ?>

    <?php // echo $form->field($model, 'sdl_message_sid') ?>

    <?php // echo $form->field($model, 'sdl_num_segments') ?>

    <?php // echo $form->field($model, 'sdl_price') ?>

    <?php // echo $form->field($model, 'sdl_created_user_id') ?>

    <?php // echo $form->field($model, 'sdl_updated_user_id') ?>

    <?php // echo $form->field($model, 'sdl_created_dt') ?>

    <?php // echo $form->field($model, 'sdl_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
