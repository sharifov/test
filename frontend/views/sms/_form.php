<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Sms */
/* @var $form yii\widgets\ActiveForm */
/* @var $phoneList [] */
?>

<div class="sms-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
    <?//= $form->field($model, 's_reply_id')->textInput() ?>



    <?//= $form->field($model, 's_project_id')->textInput() ?>

        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 's_phone_from')->dropDownList($phoneList) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 's_phone_to')->textInput(['maxlength' => true]) ?>
            </div>
        </div>



    <?= $form->field($model, 's_sms_text')->textarea(['rows' => 6]) ?>

    <?//= $form->field($model, 's_sms_data')->textarea(['rows' => 6]) ?>

    <?//= $form->field($model, 's_type_id')->textInput() ?>

    <?//= $form->field($model, 's_template_type_id')->textInput() ?>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 's_lead_id')->input('number', ['min' => 1]) ?>
            <?//= $form->field($model, 's_language_id')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">

        </div>
    </div>

    <?/*= $form->field($model, 's_communication_id')->textInput() ?>

    <?= $form->field($model, 's_is_deleted')->textInput() ?>

    <?= $form->field($model, 's_is_new')->textInput() ?>

    <?= $form->field($model, 's_delay')->textInput() ?>

    <?= $form->field($model, 's_priority')->textInput() ?>

    <?= $form->field($model, 's_status_id')->textInput() ?>

    <?= $form->field($model, 's_status_done_dt')->textInput() ?>

    <?= $form->field($model, 's_read_dt')->textInput() ?>

    <?= $form->field($model, 's_error_message')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 's_tw_price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 's_tw_sent_dt')->textInput() ?>

    <?= $form->field($model, 's_tw_account_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 's_tw_message_sid')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 's_tw_num_segments')->textInput() ?>

    <?= $form->field($model, 's_tw_to_country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 's_tw_to_state')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 's_tw_to_city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 's_tw_to_zip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 's_tw_from_country')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 's_tw_from_state')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 's_tw_from_city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 's_tw_from_zip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 's_created_user_id')->textInput() ?>

    <?= $form->field($model, 's_updated_user_id')->textInput() ?>

    <?= $form->field($model, 's_created_dt')->textInput() ?>

    <?= $form->field($model, 's_updated_dt')->textInput()*/ ?>

    <div class="form-group">
        <?= Html::submitButton('Create & Send SMS', ['class' => 'btn btn-success']) ?>
    </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
