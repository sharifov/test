<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\hotel\models\search\HotelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attraction-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'atn_id') ?>

    <?= $form->field($model, 'atn_product_id') ?>

    <?= $form->field($model, 'atn_date_from') ?>

    <?= $form->field($model, 'atn_date_to') ?>

    <?= $form->field($model, 'atn_destination_code') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
