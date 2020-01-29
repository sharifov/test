<?php

use modules\invoice\src\entities\invoice\InvoiceStatus;
use modules\invoice\src\entities\invoice\InvoiceStatusAction;
use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\invoice\src\entities\invoiceStatusLog\InvoiceStatusLog */
/* @var $form yii\widgets\ActiveForm */

$list = new ListsAccess(Auth::id());

?>

<div class="invoice-status-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'invsl_invoice_id')->textInput() ?>

        <?= $form->field($model, 'invsl_start_status_id')->dropDownList(InvoiceStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'invsl_end_status_id')->dropDownList(InvoiceStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'invsl_start_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'invsl_end_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'invsl_duration')->textInput() ?>

        <?= $form->field($model, 'invsl_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'invsl_action_id')->dropDownList(InvoiceStatusAction::getList(), ['prompt' => 'Select action']) ?>

        <?= $form->field($model, 'invsl_created_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
