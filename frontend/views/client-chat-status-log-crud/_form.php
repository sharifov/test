<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatStatusLog\entity\ClientChatStatusLog */
/* @var $form ActiveForm */
?>

<div class="client-chat-status-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'csl_cch_id')->textInput() ?>

        <?= $form->field($model, 'csl_from_status')->dropDownList(\sales\model\clientChat\entity\ClientChat::getStatusList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'csl_to_status')->dropDownList(\sales\model\clientChat\entity\ClientChat::getStatusList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'csl_start_dt')->widget(\sales\widgets\DateTimePicker::class, []) ?>

        <?= $form->field($model, 'csl_end_dt')->widget(\sales\widgets\DateTimePicker::class, []) ?>

        <?= $form->field($model, 'csl_owner_id')->widget(\sales\widgets\UserSelect2Widget::class, [
            'data' => $model->csl_owner_id ? [
                $model->csl_owner_id => $model->cslOwner->username
            ] : [],
        ]) ?>

        <?= $form->field($model, 'csl_description')->textarea(['maxlength' => true]) ?>

        <?= $form->field($model, 'csl_rid')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
