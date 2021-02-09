<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\airline\entity\AirlineSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="airline-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'iata') ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'iaco') ?>

    <?php // echo $form->field($model, 'countryCode') ?>

    <?php // echo $form->field($model, 'country') ?>

    <?php // echo $form->field($model, 'cl_economy') ?>

    <?php // echo $form->field($model, 'cl_premium_economy') ?>

    <?php // echo $form->field($model, 'cl_business') ?>

    <?php // echo $form->field($model, 'cl_premium_business') ?>

    <?php // echo $form->field($model, 'cl_first') ?>

    <?php // echo $form->field($model, 'cl_premium_first') ?>

    <?php // echo $form->field($model, 'updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
