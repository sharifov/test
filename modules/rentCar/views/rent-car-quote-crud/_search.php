<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\rentCar\src\entity\rentCarQuote\RentCarQuoteSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="rent-car-quote-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'rcq_id') ?>

    <?= $form->field($model, 'rcq_rent_car_id') ?>

    <?= $form->field($model, 'rcq_product_quote_id') ?>

    <?= $form->field($model, 'rcq_hash_key') ?>

    <?= $form->field($model, 'rcq_json_response') ?>

    <?php // echo $form->field($model, 'rcq_model_name') ?>

    <?php // echo $form->field($model, 'rcq_category') ?>

    <?php // echo $form->field($model, 'rcq_image_url') ?>

    <?php // echo $form->field($model, 'rcq_vendor_name') ?>

    <?php // echo $form->field($model, 'rcq_vendor_logo_url') ?>

    <?php // echo $form->field($model, 'rcq_transmission') ?>

    <?php // echo $form->field($model, 'rcq_seats') ?>

    <?php // echo $form->field($model, 'rcq_doors') ?>

    <?php // echo $form->field($model, 'rcq_options') ?>

    <?php // echo $form->field($model, 'rcq_days') ?>

    <?php // echo $form->field($model, 'rcq_price_per_day') ?>

    <?php // echo $form->field($model, 'rcq_currency') ?>

    <?php // echo $form->field($model, 'rcq_advantages') ?>

    <?php // echo $form->field($model, 'rcq_pick_up_location') ?>

    <?php // echo $form->field($model, 'rcq_drop_of_location') ?>

    <?php // echo $form->field($model, 'rcq_created_dt') ?>

    <?php // echo $form->field($model, 'rcq_updated_dt') ?>

    <?php // echo $form->field($model, 'rcq_created_user_id') ?>

    <?php // echo $form->field($model, 'rcq_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
