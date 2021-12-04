<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\voip\phoneDevice\PhoneDeviceLogSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="phone-device-log-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pdl_id') ?>

    <?= $form->field($model, 'pdl_user_id') ?>

    <?= $form->field($model, 'pdl_device_id') ?>

    <?= $form->field($model, 'pdl_level') ?>

    <?= $form->field($model, 'pdl_message') ?>

    <?php // echo $form->field($model, 'pdl_error') ?>

    <?php // echo $form->field($model, 'pdl_timestamp_ts') ?>

    <?php // echo $form->field($model, 'pdl_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
