<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model modules\attraction\models\AttractionPaxSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attraction-pax-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'atnp_id') ?>

    <?= $form->field($model, 'atnp_atn_id') ?>

    <?= $form->field($model, 'atnp_type_id') ?>

    <?= $form->field($model, 'atnp_age') ?>

    <?= $form->field($model, 'atnp_first_name') ?>

    <?php // echo $form->field($model, 'atnp_last_name') ?>

    <?php // echo $form->field($model, 'atnp_dob') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
