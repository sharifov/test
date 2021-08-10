<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteChange\ProductQuoteChange */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="product-quote-change-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pqc_pq_id')->textInput() ?>

    <?= $form->field($model, 'pqc_case_id')->textInput() ?>

    <?= $form->field($model, 'pqc_decision_user')->textInput() ?>

    <?= $form->field($model, 'pqc_status_id')->textInput() ?>

    <?= $form->field($model, 'pqc_decision_type_id')->textInput() ?>

    <?= $form->field($model, 'pqc_created_dt')->textInput() ?>

    <?= $form->field($model, 'pqc_updated_dt')->textInput() ?>

    <?= $form->field($model, 'pqc_decision_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
