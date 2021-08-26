<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\client\notifications\sms\entity\search\ClientNotificationSmsListSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-notification-sms-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cnsl_id') ?>

    <?= $form->field($model, 'cnsl_status_id') ?>

    <?= $form->field($model, 'cnsl_from_phone_id') ?>

    <?= $form->field($model, 'cnsl_name_from') ?>

    <?= $form->field($model, 'cnsl_to_client_phone_id') ?>

    <?= $form->field($model, 'cnsl_start') ?>

    <?php // echo $form->field($model, 'cnsl_end') ?>

    <?php // echo $form->field($model, 'cnsl_message') ?>

    <?php // echo $form->field($model, 'cnsl_created_dt') ?>

    <?php // echo $form->field($model, 'cnsl_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
