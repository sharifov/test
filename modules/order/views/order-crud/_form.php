<?php

use common\models\Currency;
use kdn\yii2\JsonEditor;
use modules\order\src\entities\order\OrderPayStatus;
use modules\order\src\entities\order\OrderStatus;
use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\order\src\entities\order\Order */
/* @var $form yii\widgets\ActiveForm */

$list = (new ListsAccess(Auth::id()));

?>

<div class="order-form">
    <?php $form = ActiveForm::begin(); ?>
    <div class="col-md-4">

        <?= $form->field($model, 'or_gid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'or_uid')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'or_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'or_lead_id')->textInput() ?>

        <?= $form->field($model, 'or_description')->textarea(['rows' => 6]) ?>

        <?= $form->field($model, 'or_status_id')->dropDownList(OrderStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'or_pay_status_id')->dropDownList(OrderPayStatus::getList(), ['prompt' => 'Select pay status']) ?>

        <?= $form->field($model, 'or_app_total')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'or_app_markup')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'or_agent_markup')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'or_client_total')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'or_client_currency')->dropDownList(Currency::getList(), ['prompt' => '---']) ?>

        <?= $form->field($model, 'or_client_currency_rate')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'or_profit_amount')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'or_owner_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <?= $form->field($model, 'or_created_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <?= $form->field($model, 'or_created_dt')->widget(DateTimePicker::class) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <div class="col-md-6">
        <?php

        $model->or_request_data = \frontend\helpers\JsonHelper::encode($model->or_request_data);

        try {
            echo $form->field($model, 'or_request_data')->widget(
                \kdn\yii2\JsonEditor::class,
                [
                    'clientOptions' => [
                        'modes' => ['code', 'form', 'tree', 'view'],
                        'mode' => 'form'
                    ],
                    'expandAll' => ['tree', 'form'],
                ]
            );
        } catch (Exception $exception) {
            echo Html::textarea($model->formName() . '[or_request_data]', Json::encode($model->or_request_data), ['class' => 'form-control']);
        }
        ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
