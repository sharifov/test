<?php

use common\models\Employee;
use sales\model\clientChatFeedback\entity\ClientChatFeedback;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatFeedback\entity\ClientChatFeedback */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-chat-feedback-form">

    <div class="col-md-4">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ccf_client_chat_id')->textInput() ?>

        <?= $form->field($model, 'ccf_user_id')->dropDownList(Employee::getList(), ['prompt' => '---'])?>

        <?= $form->field($model, 'ccf_client_id')->textInput() ?>

        <?= $form->field($model, 'ccf_rating')->dropDownList(ClientChatFeedback::RATING_LIST, ['prompt' => '---']) ?>

        <?= $form->field($model, 'ccf_message')->textarea(['rows' => 6]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
