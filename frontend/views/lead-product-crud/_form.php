<?php

use sales\model\leadProduct\entity\LeadProduct;
use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model LeadProduct */
/* @var $form ActiveForm */
?>

<div class="lead-product-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'lp_lead_id')->textInput() ?>

        <?= $form->field($model, 'lp_product_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
