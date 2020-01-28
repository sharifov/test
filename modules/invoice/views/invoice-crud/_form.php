<?php

use modules\invoice\src\entities\invoice\InvoiceStatus;
use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\invoice\src\entities\invoice\Invoice */
/* @var $form yii\widgets\ActiveForm */

$list = new ListsAccess(Auth::id());

?>

<div class="invoice-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'inv_gid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'inv_uid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'inv_order_id')->textInput() ?>

        <?= $form->field($model, 'inv_status_id')->dropDownList(InvoiceStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'inv_sum')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'inv_client_sum')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'inv_client_currency')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'inv_currency_rate')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'inv_description')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'inv_created_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <?= $form->field($model, 'inv_created_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'inv_updated_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
