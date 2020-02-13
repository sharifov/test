<?php

use modules\order\src\entities\order\OrderStatus;
use modules\order\src\entities\order\OrderStatusAction;
use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\order\src\entities\orderStatusLog\OrderStatusLog */
/* @var $form yii\widgets\ActiveForm */

$list = new ListsAccess(Auth::id());

?>

<div class="order-status-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'orsl_order_id')->textInput() ?>

        <?= $form->field($model, 'orsl_start_status_id')->dropDownList(OrderStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'orsl_end_status_id')->dropDownList(OrderStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'orsl_start_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'orsl_end_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'orsl_duration')->textInput() ?>

        <?= $form->field($model, 'orsl_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'orsl_action_id')->dropDownList(OrderStatusAction::getList(), ['prompt' => 'Select action']) ?>

        <?= $form->field($model, 'orsl_owner_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <?= $form->field($model, 'orsl_created_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
