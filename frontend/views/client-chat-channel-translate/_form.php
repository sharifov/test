<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\channelTranslate\ClientChatChannelTranslate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-channel-translate-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-4">
    <?= $form->field($model, 'ct_channel_id')->dropDownList(\sales\model\clientChatChannel\entity\ClientChatChannel::getList(), ['prompt' => '---']) ?>

    <?= $form->field($model, 'ct_language_id')->dropDownList(\common\models\Language::getLanguages(), ['prompt' => '---']) ?>

    <?= $form->field($model, 'ct_name')->textInput(['maxlength' => true]) ?>

    <?php //= $form->field($model, 'ct_created_user_id')->textInput() ?>

    <?php //= $form->field($model, 'ct_updated_user_id')->textInput() ?>

    <?php //= $form->field($model, 'ct_created_dt')->textInput() ?>

    <?php //= $form->field($model, 'ct_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
    </div>

    </div>

    <?php ActiveForm::end(); ?>

</div>
