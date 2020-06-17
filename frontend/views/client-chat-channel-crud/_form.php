<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\clientChatChannel\entity\ClientChatChannel */
/* @var $form ActiveForm */
?>

<div class="client-chat-channel-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ccc_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ccc_project_id')->textInput() ?>

        <?= $form->field($model, 'ccc_dep_id')->textInput() ?>

        <?= $form->field($model, 'ccc_ug_id')->textInput() ?>

        <?= $form->field($model, 'ccc_disabled')->checkbox() ?>

        <?php // $form->field($model, 'ccc_created_dt')->textInput() ?>

        <?php // $form->field($model, 'ccc_updated_dt')->textInput() ?>

        <?php // $form->field($model, 'ccc_created_user_id')->textInput() ?>

        <?php // $form->field($model, 'ccc_updated_user_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
