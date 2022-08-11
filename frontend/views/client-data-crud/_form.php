<?php

use src\model\clientDataKey\entity\ClientDataKey;
use src\model\clientDataKey\service\ClientDataKeyService;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\clientData\entity\ClientData */
/* @var $form ActiveForm */
?>

<div class="client-data-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cd_client_id')->textInput() ?>

        <?= $form->field($model, 'cd_key_id')->dropDownList(ClientDataKeyService::getListCache(true)) ?>

        <?= $form->field($model, 'cd_field_value')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'cd_field_value_ui')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
