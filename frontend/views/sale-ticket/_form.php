<?php

use sales\model\saleTicket\entity\SaleTicket;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model sales\model\saleTicket\entity\SaleTicket */
/* @var $form ActiveForm */
?>

<div class="sale-ticket-form">

    <?php Pjax::begin(['enableReplaceState' => false, 'enablePushState' => false, 'timeout' => 10000]) ?>

    <?php $form = ActiveForm::begin(['options' => ['data-pjax' => 1]]); ?>

    <?= $form->errorSummary($model) ?>

    <div class="col-md-2">

        <?= $form->field($model, 'st_case_id')->input('number') ?>

        <?= $form->field($model, 'st_case_sale_id')->input('number') ?>

        <?= $form->field($model, 'st_ticket_number')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'st_client_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'st_record_locator')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'st_original_fop')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'st_charge_system')->textInput(['maxlength' => true]) ?>

    </div>
    <div class="col-md-2">
        <?= $form->field($model, 'st_penalty_type')->dropDownList(SaleTicket::getAirlinePenaltyList()) ?>

        <?= $form->field($model, 'st_penalty_amount')->input('number', ['maxlength' => true]) ?>

        <?= $form->field($model, 'st_selling')->input('number', ['maxlength' => true]) ?>

        <?= $form->field($model, 'st_service_fee')->input('number', ['maxlength' => true]) ?>

        <?= $form->field($model, 'st_recall_commission')->input('number', ['maxlength' => true]) ?>

        <?= $form->field($model, 'st_markup')->input('number', ['maxlength' => true]) ?>
    </div>
    <div class="col-md-12">

        <div class="form-group">
            <?= Html::submitButton('<i class="fa fa-save"></i> Save', ['class' => 'btn btn-success']) ?>
        </div>

    </div>
    <?php ActiveForm::end(); ?>

    <?php Pjax::end() ?>

</div>
