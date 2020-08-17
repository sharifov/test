<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\AirportsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="airports-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'city') ?>

    <?= $form->field($model, 'country') ?>


    <?= $form->field($model, 'iata') ?>

    <?php // echo $form->field($model, 'latitude') ?>

    <?php // echo $form->field($model, 'longitude') ?>

    <?php // echo $form->field($model, 'timezone') ?>

    <?php // echo $form->field($model, 'dst') ?>

    <?php // echo $form->field($model, 'a_created_user_id') ?>

    <?php // echo $form->field($model, 'a_updated_user_id') ?>

    <?php // echo $form->field($model, 'a_icao') ?>

    <?php // echo $form->field($model, 'a_country_code') ?>

    <?php // echo $form->field($model, 'a_city_code') ?>

    <?php // echo $form->field($model, 'a_state') ?>

    <?php // echo $form->field($model, 'a_rank') ?>

    <?php // echo $form->field($model, 'a_multicity') ?>

    <?php // echo $form->field($model, 'a_close') ?>

    <?php // echo $form->field($model, 'a_disabled') ?>

    <?php // echo $form->field($model, 'a_created_dt') ?>

    <?php // echo $form->field($model, 'a_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
