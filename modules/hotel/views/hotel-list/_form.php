<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\HotelList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'hl_code')->textInput() ?>

    <?= $form->field($model, 'hl_hash_key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_star')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_category_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_destination_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_destination_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_zone_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_zone_code')->textInput() ?>

    <?= $form->field($model, 'hl_country_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_state_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'hl_address')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'hl_postal_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_city')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_web')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_phone_list')->textInput() ?>

    <?= $form->field($model, 'hl_image_list')->textInput() ?>

    <?= $form->field($model, 'hl_image_base_url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_board_codes')->textInput() ?>

    <?= $form->field($model, 'hl_segment_codes')->textInput() ?>

    <?= $form->field($model, 'hl_latitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_longitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_ranking')->textInput() ?>

    <?= $form->field($model, 'hl_service_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'hl_last_update')->textInput() ?>

    <?= $form->field($model, 'hl_created_dt')->textInput() ?>

    <?= $form->field($model, 'hl_updated_dt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
