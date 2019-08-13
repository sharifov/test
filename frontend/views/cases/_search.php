<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\entities\cases\CasesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cases-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'cs_id') ?>

    <?= $form->field($model, 'cs_subject') ?>

    <?= $form->field($model, 'cs_description') ?>

    <?= $form->field($model, 'cs_category') ?>

    <?= $form->field($model, 'cs_status') ?>

    <?php // echo $form->field($model, 'cs_user_id') ?>

    <?php // echo $form->field($model, 'cs_lead_id') ?>

    <?php // echo $form->field($model, 'cs_call_id') ?>

    <?php // echo $form->field($model, 'cs_depart_id') ?>

    <?php // echo $form->field($model, 'cs_created_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
