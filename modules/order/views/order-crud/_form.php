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

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

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

        <?php ActiveForm::end(); ?>
    </div>

    <div class="col-md-6">
        <?php

        try {
            echo \kdn\yii2\JsonEditor::widget(
                [
                    'clientOptions' => [
                        'modes' => ['view'], // all available modes 'code', 'form', 'preview', 'text', 'tree', 'view'
                        'mode' => 'view', // default mode
                    ],
                    //'collapseAll' => ['view'], // collapse all fields in "view" mode
                    'containerOptions' => ['class' => 'well'], // HTML options for JSON editor container tag
                    'expandAll' => ['view'], // expand all fields in "tree" and "form" modes
                    'name' => 'editor', // hidden input name
                    'options' => ['id' => 'data'], // HTML options for hidden input
                    //'value' => '{"foo": "bar"}', // JSON which should be shown in editor
                    'decodedValue' => $model->or_request_data
                ]
            );
        } catch (Exception $exception) {
            echo Html::textarea($model->formName() . '[or_request_data]', Json::encode($model->or_request_data), ['class' => 'form-control']);
        }

        ?>
    </div>

</div>
