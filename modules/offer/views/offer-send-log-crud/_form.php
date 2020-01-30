<?php

use modules\offer\src\entities\offerSendLog\OfferSendLogType;
use sales\access\ListsAccess;
use sales\auth\Auth;
use sales\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\offer\src\entities\offerSendLog\OfferSendLog */
/* @var $form yii\widgets\ActiveForm */

$list = new ListsAccess(Auth::id());

?>

<div class="offer-send-log-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'ofsndl_offer_id')->textInput() ?>

        <?= $form->field($model, 'ofsndl_type_id')->dropDownList(OfferSendLogType::getList(), ['prompt' => 'Select type']) ?>

        <?= $form->field($model, 'ofsndl_send_to')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'ofsndl_created_dt')->widget(DateTimePicker::class) ?>

        <?= $form->field($model, 'ofsndl_created_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
