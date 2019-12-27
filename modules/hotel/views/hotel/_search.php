<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\search\HotelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'ph_id') ?>

    <?= $form->field($model, 'ph_product_id') ?>

    <?= $form->field($model, 'ph_check_in_dt') ?>

    <?= $form->field($model, 'ph_check_out_dt') ?>

    <?= $form->field($model, 'ph_destination_code') ?>

    <?php // echo $form->field($model, 'ph_min_star_rate') ?>

    <?php // echo $form->field($model, 'ph_max_star_rate') ?>

    <?php // echo $form->field($model, 'ph_max_price_rate') ?>

    <?php // echo $form->field($model, 'ph_min_price_rate') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
