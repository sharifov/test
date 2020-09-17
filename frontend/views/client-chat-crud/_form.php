<?php

use sales\model\clientChat\entity\ClientChat;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChat\entity\ClientChat */
/* @var $form ActiveForm */
?>

<div class="client-chat-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cch_rid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cch_ccr_id')->textInput() ?>

        <?= $form->field($model, 'cch_title')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cch_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cch_project_id')->dropDownList(\common\models\Project::getList(), ['prompt' => '---'])?>

        <?= $form->field($model, 'cch_dep_id')->dropDownList(\common\models\Department::getList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'cch_channel_id')->dropDownList(\sales\model\clientChatChannel\entity\ClientChatChannel::getList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'cch_client_id')->textInput() ?>

        <?= $form->field($model, 'cch_owner_user_id')->widget(\sales\widgets\UserSelect2Widget::class, [
            'data' => $model->cch_owner_user_id ? [
                $model->cch_owner_user_id => $model->cchOwnerUser->username
            ] : [],
        ]) ?>

        <?= $form->field($model, 'cch_note')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cch_status_id')->dropDownList(ClientChat::getStatusList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'cch_ip')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cch_ua')->textInput() ?>

        <?= $form->field($model, 'cch_language_id')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cch_source_type_id')->dropDownList(ClientChat::getSourceTypeList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'cch_missed')->checkbox() ?>

        <?php // $form->field($model, 'cch_created_dt')->textInput() ?>

        <?php // $form->field($model, 'cch_updated_dt')->textInput() ?>

        <?php // $form->field($model, 'cch_created_user_id')->textInput() ?>

        <?php // $form->field($model, 'cch_updated_user_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
