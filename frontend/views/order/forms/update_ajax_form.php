<?php

use frontend\models\form\OrderForm;
use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model OrderForm */
/* @var $form yii\bootstrap4\ActiveForm */

$pjaxId = 'pjax-order-form';
?>

<div class="order-form">

    <script>
        pjaxOffFormSubmit('#<?=$pjaxId?>');
    </script>
    <?php \yii\widgets\Pjax::begin(['id' => $pjaxId, 'timeout' => 5000, 'enablePushState' => false, 'enableReplaceState' => false]); ?>
    <?php
    $form = ActiveForm::begin([
        'options' => ['data-pjax' => true],
        'action' => ['/order/update-ajax', 'id' => $model->or_id],
        'method' => 'post'
    ]);
    ?>

    <?php echo $form->errorSummary($model) ?>

    <?= $form->field($model, 'or_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'or_name')->textInput(['maxlength' => true]) ?>



    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'or_status_id')->dropDownList(\common\models\Order::getStatusList(), ['prompt' => '---']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'or_pay_status_id')->dropDownList(\common\models\Order::getPayStatusList(), ['prompt' => '---']) ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'or_app_total')->input('number', ['min' => 0, 'max' => 10000, 'step' => 0.01]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'or_client_total')->input('number', ['min' => 0, 'max' => 10000, 'step' => 0.01]) ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'or_app_markup')->input('number', ['min' => 0, 'max' => 10000, 'step' => 0.01]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'or_agent_markup')->input('number', ['min' => 0, 'max' => 10000, 'step' => 0.01]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'or_client_currency')->dropDownList(\common\models\Currency::getList(), ['prompt' => '---']) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'or_client_currency_rate')->input('number', ['min' => 0, 'max' => 40, 'step' => 0.00001]) ?>
        </div>
    </div>

    <div class="form-group text-center">
        <?= Html::submitButton('<i class="fa fa-save"></i> Save order', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>