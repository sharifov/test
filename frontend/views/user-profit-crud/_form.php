<?php

use common\models\Employee;
use sales\model\user\profit\UserProfit;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\user\profit\UserProfit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-profit-form">

    <?php \yii\widgets\Pjax::begin() ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->errorSummary($model) ?>
        </div>

        <div class="col-md-3">
            <?php $url = \yii\helpers\Url::toRoute(['/user-profit-crud/get-agent-percent-commission']) ?>
            <?= $form->field($model, 'up_user_id')->widget(\kartik\select2\Select2::class, [
                'data' => Employee::getList(),
                'pluginOptions' => [
					'placeholder' => 'Select user...'
                ],
                'pluginEvents' => [
                    'change' => New \yii\web\JsExpression(' function (event) {
                        let userId = $(this).val();
                        $.post( "'.$url.'", {userId: userId}, function (data) {
                            let agentCommission = data.agentCommission || 0;
                            $("#agentCommission").val(agentCommission);
                        });
                    }')
                ]
            ]) ?>

            <?= $form->field($model, 'up_lead_id')->textInput() ?>

            <?= $form->field($model, 'up_order_id')->textInput() ?>

            <?= $form->field($model, 'up_product_quote_id')->textInput() ?>

            <?= $form->field($model, 'up_percent')->textInput(['id' => 'agentCommission']) ?>

            <?= $form->field($model, 'up_profit')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'up_split_percent')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'up_status_id')->dropDownList(UserProfit::getStatusList(), ['prompt' => '--']) ?>

            <?= $form->field($model, 'up_payroll_id')->textInput() ?>

            <?= $form->field($model, 'up_type_id')->dropDownList(UserProfit::getTypeList(), ['prompt' => '--']) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php \yii\widgets\Pjax::end() ?>

</div>
