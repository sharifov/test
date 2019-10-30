<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ConferenceSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="conference-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'cf_id') ?>

    <?= $form->field($model, 'cf_cr_id') ?>

    <?= $form->field($model, 'cf_sid') ?>

    <?= $form->field($model, 'cf_status_id') ?>

    <?= $form->field($model, 'cf_options') ?>

    <?php // echo $form->field($model, 'cf_created_dt') ?>

    <?php // echo $form->field($model, 'cf_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
