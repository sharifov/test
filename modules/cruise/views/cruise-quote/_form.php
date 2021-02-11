<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruiseQuote\CruiseQuote */
/* @var $form ActiveForm */
?>

<div class="cruise-quote-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'crq_hash_key')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'crq_product_quote_id')->textInput() ?>

        <?= $form->field($model, 'crq_cruise_id')->textInput() ?>

        <?= $form->field($model, 'crq_data_json')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
