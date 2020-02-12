<?php

use common\models\Employee;
use sales\model\user\payment\UserPayment;
use sales\model\user\paymentCategory\UserPaymentCategory;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model sales\model\user\payment\UserPayment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-payment-form">

    <?php Pjax::begin(); ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>
    <div class="row">
        <div class="col-md-3">

            <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'upt_assigned_user_id')->dropDownList(Employee::getList(), ['prompt' => '--']) ?>

            <?= $form->field($model, 'upt_category_id')->dropDownList(UserPaymentCategory::getList(), ['prompt' => '--']) ?>

            <?= $form->field($model, 'upt_status_id')->dropDownList(UserPayment::getStatusList(), ['prompt' => '--']) ?>

            <?= $form->field($model, 'upt_amount')->textInput(['maxlength' => true, 'type' => 'number', 'step' => '000000.01']) ?>

            <?= $form->field($model, 'upt_description')->textarea(['maxlength' => true]) ?>

            <?= $form->field($model, 'upt_date')->widget(\kartik\date\DatePicker::class, [
				'pluginOptions' => [
					'format' => 'yyyy-mm-dd',
					'autoclose' => true,
				]
            ]) ?>

            <?= $form->field($model, 'upt_payroll_id')->textInput() ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php Pjax::end(); ?>

</div>
