<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\ClentPhoneSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-phone-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'client_id') ?>

    <?= $form->field($model, 'phone') ?>

    <?= $form->field($model, 'created') ?>

    <?= $form->field($model, 'updated') ?>

    <?php // echo $form->field($model, 'comments') ?>

    <?php // echo $form->field($model, 'is_sms') ?>

    <?php // echo $form->field($model, 'validate_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
