<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelQuote */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-quote-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'hq_hotel_id')->textInput() ?>

    <?= $form->field($model, 'hq_hash_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hq_product_quote_id')->textInput() ?>

    <?= $form->field($model, 'hq_json_response')->textInput() ?>

    <?= $form->field($model, 'hq_destination_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hq_hotel_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hq_hotel_list_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
