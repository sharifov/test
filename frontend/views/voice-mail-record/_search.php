<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\voiceMailRecord\entity\search\VoiceMailRecordSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="voice-mail-record-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'vmr_call_id') ?>

    <?= $form->field($model, 'vmr_record_sid') ?>

    <?= $form->field($model, 'vmr_client_id') ?>

    <?= $form->field($model, 'vmr_user_id') ?>

    <?= $form->field($model, 'vmr_created_dt') ?>

    <?php // echo $form->field($model, 'vmr_duration') ?>

    <?php // echo $form->field($model, 'vmr_new') ?>

    <?php // echo $form->field($model, 'vmr_deleted') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
