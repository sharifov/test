<?php

use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatHold\entity\ClientChatHold */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-hold-form">

    <div class="col-md-4">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cchd_cch_id')->textInput() ?>

        <?= $form->field($model, 'cchd_cch_status_log_id')->textInput() ?>

        <?php echo $form->field($model, 'cchd_start_dt')->widget(DateTimePicker::class) ?>

        <?php echo $form->field($model, 'cchd_deadline_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
