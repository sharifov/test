<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\voip\phoneDevice\device\PhoneDeviceSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="phone-device-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pd_id') ?>

    <?= $form->field($model, 'pd_user_id') ?>

    <?= $form->field($model, 'pd_name') ?>

    <?= $form->field($model, 'pd_device_identity') ?>

    <?php // echo $form->field($model, 'pd_status_device') ?>

    <?php // echo $form->field($model, 'pd_status_speaker') ?>

    <?php // echo $form->field($model, 'pd_status_microphone') ?>

    <?php // echo $form->field($model, 'pd_ip_address') ?>

    <?php // echo $form->field($model, 'pd_created_dt') ?>

    <?php // echo $form->field($model, 'pd_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
