<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\client\notifications\phone\entity\search\ClientNotificationPhoneListSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="client-notification-phone-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cnfl_id') ?>

    <?= $form->field($model, 'cnfl_status_id') ?>

    <?= $form->field($model, 'cnfl_from_phone_id') ?>

    <?= $form->field($model, 'cnfl_to_client_phone_id') ?>

    <?= $form->field($model, 'cnfl_start') ?>

    <?php // echo $form->field($model, 'cnfl_end') ?>

    <?php // echo $form->field($model, 'cnfl_message') ?>

    <?php // echo $form->field($model, 'cnfl_file_url') ?>

    <?php // echo $form->field($model, 'cnfl_call_sid') ?>

    <?php // echo $form->field($model, 'cnfl_created_dt') ?>

    <?php // echo $form->field($model, 'cnfl_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
