<?php

use sales\access\ListsAccess;
use sales\auth\Auth;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model \modules\order\src\entities\orderProduct\OrderProduct */
/* @var $form yii\widgets\ActiveForm */

$list = (new ListsAccess(Auth::id()));

?>

<div class="order-product-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'orp_order_id')->textInput() ?>

        <?= $form->field($model, 'orp_product_quote_id')->textInput() ?>

        <?= $form->field($model, 'orp_created_user_id')->dropDownList($list->getEmployees(), ['prompt' => 'Select user']) ?>

        <? //= $form->field($model, 'orp_created_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
