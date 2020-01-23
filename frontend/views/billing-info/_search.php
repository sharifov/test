<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\BillingInfoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="billing-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'bi_id') ?>

    <?= $form->field($model, 'bi_first_name') ?>

    <?= $form->field($model, 'bi_last_name') ?>

    <?= $form->field($model, 'bi_middle_name') ?>

    <?= $form->field($model, 'bi_company_name') ?>

    <?php // echo $form->field($model, 'bi_address_line1') ?>

    <?php // echo $form->field($model, 'bi_address_line2') ?>

    <?php // echo $form->field($model, 'bi_city') ?>

    <?php // echo $form->field($model, 'bi_state') ?>

    <?php // echo $form->field($model, 'bi_country') ?>

    <?php // echo $form->field($model, 'bi_zip') ?>

    <?php // echo $form->field($model, 'bi_contact_phone') ?>

    <?php // echo $form->field($model, 'bi_contact_email') ?>

    <?php // echo $form->field($model, 'bi_contact_name') ?>

    <?php // echo $form->field($model, 'bi_payment_method_id') ?>

    <?php // echo $form->field($model, 'bi_cc_id') ?>

    <?php // echo $form->field($model, 'bi_order_id') ?>

    <?php // echo $form->field($model, 'bi_status_id') ?>

    <?php // echo $form->field($model, 'bi_created_user_id') ?>

    <?php // echo $form->field($model, 'bi_updated_user_id') ?>

    <?php // echo $form->field($model, 'bi_created_dt') ?>

    <?php // echo $form->field($model, 'bi_updated_dt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
