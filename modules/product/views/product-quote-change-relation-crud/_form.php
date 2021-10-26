<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\product\src\entities\productQuoteChangeRelation\ProductQuoteChangeRelation */
/* @var $form ActiveForm */
?>

<div class="product-quote-change-relation-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'pqcr_pqc_id')->textInput() ?>

        <?= $form->field($model, 'pqcr_pq_id')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
