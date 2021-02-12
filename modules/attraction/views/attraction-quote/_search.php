<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\search\HotelQuoteSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hotel-quote-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'hq_id') ?>

    <?= $form->field($model, 'hq_hotel_id') ?>

    <?= $form->field($model, 'hq_hash_key') ?>

    <?= $form->field($model, 'hq_product_quote_id') ?>

    <?php // echo $form->field($model, 'hq_destination_name') ?>

    <?php // echo $form->field($model, 'hq_hotel_name') ?>

    <?php // echo $form->field($model, 'hq_hotel_list_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
