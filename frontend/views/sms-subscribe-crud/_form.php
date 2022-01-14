<?php

use src\model\smsSubscribe\entity\SmsSubscribeStatus;
use src\widgets\DateTimePicker;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model src\model\smsSubscribe\entity\SmsSubscribe */
/* @var $form ActiveForm */
?>

<div class="sms-subscribe-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ss_cpl_id')->textInput() ?>

        <?= $form->field($model, 'ss_sms_id')->textInput() ?>

        <?= $form->field($model, 'ss_project_id')->dropDownList(\common\models\Project::getList()) ?>

        <?= $form->field($model, 'ss_status_id')->dropDownList(SmsSubscribeStatus::STATUS_LIST) ?>

        <?= $form->field($model, 'ss_deadline_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
