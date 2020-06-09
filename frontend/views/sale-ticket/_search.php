<?php

use yii\bootstrap4\Html;
use common\components\bootstrap4\activeForm\ActiveForm;

/* @var $this yii\web\View */
/* @var $model sales\model\saleTicket\entity\search\SaleTicketSearch */
/* @var $form common\components\bootstrap4\activeForm\ActiveForm */
?>

<div class="sale-ticket-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'st_case_id') ?>

    <?= $form->field($model, 'st_case_sale_id') ?>

    <?= $form->field($model, 'st_ticket_number') ?>

    <?= $form->field($model, 'st_record_locator') ?>

    <?= $form->field($model, 'st_original_fop') ?>

    <?php // echo $form->field($model, 'st_charge_system') ?>

    <?php // echo $form->field($model, 'st_penalty_type') ?>

    <?php // echo $form->field($model, 'st_penalty_amount') ?>

    <?php // echo $form->field($model, 'st_selling') ?>

    <?php // echo $form->field($model, 'st_service_fee') ?>

    <?php // echo $form->field($model, 'st_recall_commission') ?>

    <?php // echo $form->field($model, 'st_markup') ?>

    <?php // echo $form->field($model, 'st_upfront_charge') ?>

    <?php // echo $form->field($model, 'st_refundable_amount') ?>

    <?php // echo $form->field($model, 'st_created_dt') ?>

    <?php // echo $form->field($model, 'st_updated_dt') ?>

    <?php // echo $form->field($model, 'st_created_user_id') ?>

    <?php // echo $form->field($model, 'st_updated_user_id') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
