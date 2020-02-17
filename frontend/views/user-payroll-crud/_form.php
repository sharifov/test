<?php

use common\models\Employee;
use sales\model\user\entity\payroll\UserPayroll;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\user\entity\payroll\UserPayroll */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-payroll-form">

    <?php \yii\widgets\Pjax::begin() ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->errorSummary($model) ?>

            <?= $form->field($model, 'ups_user_id')->dropDownList(Employee::getList()) ?>

            <?= $form->field($model, 'ups_month')->input('number') ?>

			<?= $form->field($model, 'ups_year')->input('number') ?>

            <?= $form->field($model, 'ups_base_amount')->textInput(['maxlength' => true, 'type' => 'number', 'step' => 0.01]) ?>

            <?= $form->field($model, 'ups_profit_amount')->textInput(['maxlength' => true, 'type' => 'number', 'step' => 0.01]) ?>

            <?= $form->field($model, 'ups_tax_amount')->textInput(['maxlength' => true, 'type' => 'number', 'step' => 0.01]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'ups_payment_amount')->textInput(['maxlength' => true, 'type' => 'number', 'step' => 0.01]) ?>

            <?= $form->field($model, 'ups_total_amount')->textInput(['maxlength' => true, 'type' => 'number', 'step' => 0.01]) ?>

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
