<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\PaymentMethodSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="payment-method-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'pm_id') ?>

    <?= $form->field($model, 'pm_name') ?>

    <?= $form->field($model, 'pm_short_name') ?>

    <?= $form->field($model, 'pm_enabled') ?>

    <?= $form->field($model, 'pm_category_id') ?>

    <?php // echo $form->field($model, 'pm_updated_user_id') ?>

    <?php // echo $form->field($model, 'pm_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
