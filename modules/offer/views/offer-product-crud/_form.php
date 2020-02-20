<?php

use sales\access\ListsAccess;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\offer\src\entities\offerProduct\OfferProduct */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="offer-product-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'op_offer_id')->textInput() ?>

        <?= $form->field($model, 'op_product_quote_id')->textInput() ?>

        <?= $form->field($model, 'op_created_user_id')->dropDownList((new ListsAccess(Auth::id()))->getEmployees(), ['prompt' => 'Select user']) ?>

        <?php //= $form->field($model, 'op_created_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
