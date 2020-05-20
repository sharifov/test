<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\SaleCreditCard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-credit-card-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'scc_sale_id')->textInput() ?>

    <?= $form->field($model, 'scc_cc_id')->textInput() ?>

    <?= $form->field($model, 'scc_created_dt')->textInput() ?>

    <?= $form->field($model, 'scc_created_user_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
