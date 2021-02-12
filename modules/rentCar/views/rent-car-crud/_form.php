<?php

use yii\bootstrap4\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\rentCar\src\entity\rentCar\RentCar */
/* @var $form ActiveForm */
?>

<div class="rent-car-form">

    <div class="col-md-4">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'prc_product_id')->textInput() ?>

        <?= $form->field($model, 'prc_pick_up_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'prc_drop_off_code')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'prc_pick_up_date')->textInput() ?>

        <?= $form->field($model, 'prc_drop_off_date')->textInput() ?>

        <?= $form->field($model, 'prc_pick_up_time')->textInput() ?>

        <?= $form->field($model, 'prc_drop_off_time')->textInput() ?>

        <?= $form->field($model, 'prc_request_hash_key')->textInput() ?>


        <div class="form-group">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
