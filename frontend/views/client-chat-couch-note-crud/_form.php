<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\ClientChatCouchNote\entity\ClientChatCouchNote */
/* @var $form ActiveForm */
?>

<div class="client-chat-couch-note-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cccn_cch_id')->textInput() ?>

        <?= $form->field($model, 'cccn_rid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cccn_message')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'cccn_alias')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
