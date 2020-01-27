<?php

use modules\offer\src\entities\offer\OfferStatus;
use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerStatusLog\OfferStatusLog */
/* @var $form yii\widgets\ActiveForm */

$list = new ListsAccess(Auth::id());

?>

<div class="offer-status-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'osl_offer_id')->textInput() ?>

        <?= $form->field($model, 'osl_start_status_id')->dropDownList(OfferStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'osl_end_status_id')->dropDownList(OfferStatus::getList(), ['prompt' => 'Select status']) ?>

        <?= $form->field($model, 'osl_start_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'osl_end_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'osl_duration')->textInput() ?>

        <?= $form->field($model, 'osl_description')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'osl_owner_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <?= $form->field($model, 'osl_created_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
