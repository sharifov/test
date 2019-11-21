<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'or_id') ?>

    <?= $form->field($model, 'or_gid') ?>

    <?= $form->field($model, 'or_uid') ?>

    <?= $form->field($model, 'or_name') ?>

    <?= $form->field($model, 'or_lead_id') ?>

    <?php // echo $form->field($model, 'or_description') ?>

    <?php // echo $form->field($model, 'or_status_id') ?>

    <?php // echo $form->field($model, 'or_pay_status_id') ?>

    <?php // echo $form->field($model, 'or_app_total') ?>

    <?php // echo $form->field($model, 'or_app_markup') ?>

    <?php // echo $form->field($model, 'or_agent_markup') ?>

    <?php // echo $form->field($model, 'or_client_total') ?>

    <?php // echo $form->field($model, 'or_client_currency') ?>

    <?php // echo $form->field($model, 'or_client_currency_rate') ?>

    <?php // echo $form->field($model, 'or_owner_user_id') ?>

    <?php // echo $form->field($model, 'or_created_user_id') ?>

    <?php // echo $form->field($model, 'or_updated_user_id') ?>

    <?php // echo $form->field($model, 'or_created_dt') ?>

    <?php // echo $form->field($model, 'or_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
