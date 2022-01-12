<?php

use src\model\client\notifications\client\entity\CommunicationType;
use src\model\client\notifications\client\entity\NotificationType;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\client\notifications\client\entity\ClientNotification */
/* @var $form ActiveForm */
?>

<div class="client-notification-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'cn_client_id')->textInput() ?>

        <?= $form->field($model, 'cn_notification_type_id')->dropDownList(NotificationType::getList(), ['prompt' => 'Select type']) ?>

        <?= $form->field($model, 'cn_object_id')->textInput() ?>

        <?= $form->field($model, 'cn_communication_type_id')->dropDownList(CommunicationType::getList(), ['prompt' => 'Select communication type']) ?>

        <?= $form->field($model, 'cn_communication_object_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
