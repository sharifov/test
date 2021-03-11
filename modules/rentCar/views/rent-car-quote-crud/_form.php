<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\rentCar\src\entity\rentCarQuote\RentCarQuote */
/* @var $form ActiveForm */
?>

<div class="rent-car-quote-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'rcq_rent_car_id')->textInput() ?>

        <?= $form->field($model, 'rcq_product_quote_id')->textInput() ?>

        <?= $form->field($model, 'rcq_hash_key')->textInput(['maxlength' => true]) ?>


        <?= $form->field($model, 'rcq_model_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'rcq_category')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'rcq_image_url')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'rcq_vendor_name')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'rcq_vendor_logo_url')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'rcq_transmission')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'rcq_seats')->textInput() ?>

        <?= $form->field($model, 'rcq_doors')->textInput(['maxlength' => true]) ?>


        <?= $form->field($model, 'rcq_days')->textInput() ?>

        <?= $form->field($model, 'rcq_price_per_day')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'rcq_currency')->textInput(['maxlength' => true]) ?>



        <?= $form->field($model, 'rcq_pick_up_location')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'rcq_drop_of_location')->textInput(['maxlength' => true]) ?>


        <?= $form->field($model, 'rcq_request_hash_key')->textInput() ?>

        <?= $form->field($model, 'rcq_pick_up_dt')->textInput() ?>
        <?= $form->field($model, 'rcq_drop_off_dt')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
