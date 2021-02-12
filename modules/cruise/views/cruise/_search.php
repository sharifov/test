<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\cruise\src\entity\cruise\search\CruiseSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="cruise-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'crs_id') ?>

    <?= $form->field($model, 'crs_product_id') ?>

    <?= $form->field($model, 'crs_departure_date_from') ?>

    <?= $form->field($model, 'crs_arrival_date_to') ?>

    <?= $form->field($model, 'crs_destination_code') ?>

    <?php // echo $form->field($model, 'crs_destination_label') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
