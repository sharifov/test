<?php

use common\models\Employee;
use sales\model\user\paymentCategory\UserPaymentCategory;
use sales\model\user\payroll\UserPayroll;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\user\payroll\UserPayroll */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-payroll-form">

    <?php \yii\widgets\Pjax::begin() ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'ups_user_id')->dropDownList(Employee::getList()) ?>

            <?php $model->ups_month = $model->ups_month ? date('F', strtotime($model->ups_month)) : date('F'); ?>
            <?= $form->field($model, 'ups_month')->widget(\kartik\date\DatePicker::class, [
                'pluginOptions' => [
                    'format' => 'MM',
                    'minViewMode'=>'months',
					'autoclose' => true,
					'todayHighlight' => true
                ]
            ]) ?>

			<?php $model->ups_year = $model->ups_year ?: date('Y'); ?>
			<?= $form->field($model, 'ups_year')->widget(\kartik\date\DatePicker::class, [
				'pluginOptions' => [
					'format' => 'yyyy',
					'minViewMode'=>'years',
					'autoclose' => true,
					'todayHighlight' => true,
				]
            ]) ?>

            <?= $form->field($model, 'ups_base_amount')->textInput(['maxlength' => true, 'type' => 'number']) ?>

            <?= $form->field($model, 'ups_profit_amount')->textInput(['maxlength' => true, 'type' => 'number']) ?>

            <?= $form->field($model, 'ups_tax_amount')->textInput(['maxlength' => true, 'type' => 'number']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'ups_payment_amount')->textInput(['maxlength' => true, 'type' => 'number']) ?>

            <?= $form->field($model, 'ups_total_amount')->textInput(['maxlength' => true, 'type' => 'number']) ?>

            <?= $form->field($model, 'ups_agent_status_id')->dropDownList(UserPayroll::getAgentStatusList(), ['prompt' => '--']) ?>

            <?= $form->field($model, 'ups_status_id')->dropDownList(UserPayroll::getStatusList(), ['prompt' => '--']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php \yii\widgets\Pjax::end() ?>

</div>
