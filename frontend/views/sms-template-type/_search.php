<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\SmsTemplateTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sms-template-type-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'stp_id') ?>

    <?= $form->field($model, 'stp_key') ?>

    <?= $form->field($model, 'stp_origin_name') ?>

    <?= $form->field($model, 'stp_name') ?>

    <?= $form->field($model, 'stp_hidden') ?>

    <?php // echo $form->field($model, 'stp_created_user_id') ?>

    <?php // echo $form->field($model, 'stp_updated_user_id') ?>

    <?php // echo $form->field($model, 'stp_created_dt') ?>

    <?php // echo $form->field($model, 'stp_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
